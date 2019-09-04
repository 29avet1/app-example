<?php

namespace App\Repositories;

use App\Contracts\Repositories\InvoiceRepositoryInterface;
use App\Contact;
use App\Invoice;
use App\InvoiceItem;
use App\Team;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class InvoiceRepository extends Repository implements InvoiceRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string
    {
        return Invoice::class;
    }

    /**
     * Create new invoice
     *
     * @param array    $invoiceData
     * @param Team $team
     * @return Invoice
     * @throws Exception
     */
    public function create(array $invoiceData, Team $team): Invoice
    {
        try {
            DB::beginTransaction();

            $contact = Contact::findByUid($invoiceData['contact_id']);

            $invoice = Invoice::create([
                'uid'            => Uuid::uuid4(),
                'user_id'        => auth()->id(),
                'team_id'    => $team->id,
                'contact_id'    => $contact->id,
                'invoice_number' => $invoiceData['invoice_number'],
                'status'         => 'draft',
                'order_status'   => 'unfulfilled',
                'message'        => '',
                'shipping'       => $invoiceData['purchase_summary']['shipping'],
                'shipping_cost'  => $invoiceData['purchase_summary']['shipping'] ? $invoiceData['purchase_summary']['shipping_cost'] : null,
            ]);

            $this->storeItems($invoiceData['purchase_summary']['items'], $invoice);
            $invoice->load('items');

            DB::commit();

            return $invoice;
        } catch (Exception $exception) {
            DB::rollBack();

            $this->handleError($exception);
        }
    }

    /**
     * Store items after create invoice
     *
     * @param array   $invoiceItems
     * @param Invoice $invoice
     * @return bool
     */
    public static function storeItems(array $invoiceItems, Invoice $invoice): bool
    {
        $now = Carbon::now();
        foreach ($invoiceItems as $key => $item) {
            $invoiceItems[$key]['invoice_id'] = $invoice->id;
            $invoiceItems[$key]['currency'] = $invoice->currency;
            $invoiceItems[$key]['tax'] = 0;
            $invoiceItems[$key]['created_at'] = $now;
            $invoiceItems[$key]['updated_at'] = $now;
        }

        return InvoiceItem::insert($invoiceItems);
    }

    /**
     * @return string
     */
    public function getNextInvoiceNumber(): string
    {
        $lastInvoice = Invoice::orderByDesc('created_at')->first();

        if (!$lastInvoice) {
            return '0001';
        }

        $currentInvoiceNumber = $lastInvoice->invoice_number;
        if (is_numeric($currentInvoiceNumber)) {
            $nextInvoiceNumber = num_prefix((string)++$currentInvoiceNumber, '0', 4);
        } else {
            if (preg_match('#(\d+)$#', $currentInvoiceNumber, $matches)) {
                $numberPrefix = preg_replace('#(\d+)$#', '', $currentInvoiceNumber);
                $nextInvoiceNumber = $numberPrefix . num_prefix((string)++$matches[1], '0', 4);
            } else {
                $nextInvoiceNumber = $currentInvoiceNumber . '0001';
            }
        }

        return $nextInvoiceNumber;
    }
}
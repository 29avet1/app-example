<?php namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\InvoiceRepositoryInterface;
use App\Http\Requests\InvoiceCreateRequest;
use App\Http\Resources\InvoiceResource;
use App\Invoice;
use App\Team;
use App\Repositories\InvoiceRepository;
use Carbon\Carbon;

/**
 * Class InvoicesController
 * @package App\Http\Controllers\Api
 */
class InvoicesController extends ApiController
{
    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;

    /**
     * InvoicesController constructor.
     * @param InvoiceRepositoryInterface $invoiceRepository
     */
    public function __construct(InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }


    /**
     * Get all invoices by user teams
     *
     * @param InvoiceListRequest $request
     * @param Team           $team
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Get(
     *      path="/teams/{team_id}/invoices",
     *      tags={"Invoices"},
     *      summary="Get all invoices by user teams",
     *      operationId="api.invoices.all",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          type="string",
     *          default="Bearer <token>",
     *          description="Authorization"
     *      ),
     *      @SWG\Parameter(
     *          name="team_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Parameter(
     *          type="integer",
     *          name="limit",
     *          in="query",
     *          required=false,
     *          default=10,
     *      ),
     *      @SWG\Parameter(
     *          type="integer",
     *          name="page",
     *          in="query",
     *          required=false,
     *          default=1,
     *      ),
     *      @SWG\Parameter(
     *          type="string",
     *          name="search_query",
     *          in="query",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          type="string",
     *          name="status",
     *          in="query",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          type="integer",
     *          name="start_date",
     *          in="query",
     *          description="Timestamp",
     *          required=false,
     *      ),
     *      @SWG\Parameter(
     *          type="integer",
     *          name="end_date",
     *          in="query",
     *          description="Timestamp",
     *          required=false,
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(
     *              property="data",
     *              type="array",
     *              @SWG\Items(
     *                  @SWG\Property(property="id", type="string"),
     *                  @SWG\Property(property="invoice_number", type="string"),
     *                  @SWG\Property(property="status", type="string"),
     *                  @SWG\Property(property="order_status", type="string"),
     *                  @SWG\Property(property="platform", type="string"),
     *                  @SWG\Property(property="paid_at", type="string", format="date-time"),
     *                  @SWG\Property(property="refunded_at", type="string", format="date-time"),
     *                  @SWG\Property(property="fulfilled_at", type="string", format="date-time"),
     *                  @SWG\Property(property="created_at", type="string", format="date-time"),
     *                  @SWG\Property(property="updated_at", type="string", format="date-time"),
     *                  @SWG\Property(
     *                    property="contact",
     *                    type="object",
     *                    @SWG\Property(property="name", type="string"),
     *                    @SWG\Property(property="phone", type="string"),
     *                    @SWG\Property(property="legal_name", type="string"),
     *                  ),
     *                  @SWG\Property(
     *                    property="purchase_summary",
     *                    type="object",
     *                    @SWG\Property(property="shipping", type="boolean"),
     *                    @SWG\Property(property="shipping_cost", type="number"),
     *                    @SWG\Property(property="total", type="number"),
     *                    @SWG\Property(property="subtotal", type="number"),
     *                    @SWG\Property(property="currency", type="string"),
     *                    @SWG\Property(property="tax", type="number"),
     *                    @SWG\Property(property="refund_balance", type="number"),
     *                  ),
     *              )
     *            ),
     *            @SWG\Property(
     *               property="links",
     *               type="object",
     *               @SWG\Property(property="first", type="string"),
     *               @SWG\Property(property="last", type="string"),
     *               @SWG\Property(property="prev", type="string"),
     *               @SWG\Property(property="next", type="string"),
     *            ),
     *            @SWG\Property(
     *               property="meta",
     *               type="object",
     *               @SWG\Property(property="current_page", type="integer"),
     *               @SWG\Property(property="from", type="integer"),
     *               @SWG\Property(property="last_page", type="integer"),
     *               @SWG\Property(property="path", type="string"),
     *               @SWG\Property(property="per_page", type="integer"),
     *               @SWG\Property(property="to", type="integer"),
     *               @SWG\Property(property="total", type="integer"),
     *            ),
     *          )
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *     @SWG\Response(
     *          response=403,
     *          description="Permission Denied"
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *     @SWG\Response(
     *          response=404,
     *          description="Item not found"
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function index(InvoiceListRequest $request, Team $team)
    {
        $this->authorize('viewInvoices', $team);

        $filters = $request->only(['limit', 'search_query', 'status', 'start_date', 'end_date']);
        $invoices = $this->invoiceRepository->getList($team, $filters);

        return InvoiceListResource::collection($invoices);
    }

    /**
     * Create new invoice
     *
     * @param InvoiceCreateRequest $request
     * @param Team             $team
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *      path="/teams/{team_id}/invoices",
     *      tags={"Invoices"},
     *      operationId="api.invoices.create",
     *      summary="Create new invoice",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="team_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *     @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          type="string",
     *          default="Bearer <token>",
     *          description="Authorization"
     *      ),
     *     @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="invoice_number", type="string"),
     *            @SWG\Property(property="message", type="string"),
     *            @SWG\Property(property="platform", type="string"),
     *            @SWG\Property(
     *              property="contact",
     *              type="object",
     *                 @SWG\Property(property="phone", type="string"),
     *                 @SWG\Property(property="name", type="string"),
     *            ),
     *            @SWG\Property(
     *              property="purchase_summary",
     *              type="object",
     *              @SWG\Property(property="shipping", type="boolean"),
     *              @SWG\Property(property="shipping_cost", type="number"),
     *              @SWG\Property(
     *                 property="items",
     *                 type="array",
     *                 @SWG\Items(
     *                   @SWG\Property(property="name", type="string"),
     *                   @SWG\Property(property="description", type="string"),
     *                   @SWG\Property(property="amount", type="integer"),
     *                   @SWG\Property(property="quantity", type="integer"),
     *                 ),
     *               ),
     *            ),
     *          )
     *      ),
     *          @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="id", type="string"),
     *            @SWG\Property(property="invoice_number", type="string"),
     *            @SWG\Property(property="message", type="string"),
     *            @SWG\Property(property="status", type="string"),
     *            @SWG\Property(property="platform", type="string"),
     *            @SWG\Property(property="application_fee", type="number", description="application fee in US cents"),
     *            @SWG\Property(property="receipt_pdf", type="string"),
     *            @SWG\Property(property="paid_at", type="string", format="date-time"),
     *            @SWG\Property(property="refunded_at", type="string", format="date-time"),
     *            @SWG\Property(property="created_at", type="string", format="date-time"),
     *            @SWG\Property(property="updated_at", type="string", format="date-time"),
     *            @SWG\Property(
     *              property="payment_provider",
     *              type="object",
     *                 @SWG\Property(property="provider", type="string"),
     *                 @SWG\Property(property="public_key", type="string"),
     *                 @SWG\Property(property="currency", type="string"),
     *            ),
     *            @SWG\Property(
     *              property="order",
     *              type="object",
     *                 @SWG\Property(property="shipping", type="boolean"),
     *                 @SWG\Property(property="status", type="string"),
     *                 @SWG\Property(property="tracking_company", type="string"),
     *                 @SWG\Property(property="tracking_number", type="string"),
     *                 @SWG\Property(property="tracking_url", type="string"),
     *                 @SWG\Property(property="fulfilled_at", type="string", format="date-time"),
     *            ),
     *            @SWG\Property(
     *              property="contact",
     *              type="object",
     *                 @SWG\Property(property="id", type="string"),
     *                 @SWG\Property(property="phone", type="string"),
     *                 @SWG\Property(property="platform", type="string"),
     *                 @SWG\Property(property="name", type="string"),
     *                 @SWG\Property(property="legal_name", type="string"),
     *                 @SWG\Property(property="avatar", type="string"),
     *                 @SWG\Property(
     *                      property="address",
     *                      type="object",
     *                      @SWG\Property(property="city", type="string"),
     *                      @SWG\Property(property="line", type="string"),
     *                      @SWG\Property(property="postal_code", type="string"),
     *                      @SWG\Property(property="state", type="string"),
     *                      @SWG\Property(
     *                          property="country",
     *                          type="object",
     *                          @SWG\Property(property="name", type="string"),
     *                          @SWG\Property(property="code", type="string"),
     *                      ),
     *                 ),
     *            ),
     *            @SWG\Property(
     *              property="purchase_summary",
     *              type="object",
     *              @SWG\Property(property="shipping", type="boolean"),
     *              @SWG\Property(property="shipping_cost", type="number"),
     *              @SWG\Property(property="total", type="number"),
     *              @SWG\Property(property="subtotal", type="number"),
     *              @SWG\Property(property="currency", type="string"),
     *              @SWG\Property(property="tax", type="number"),
     *              @SWG\Property(
     *                 property="items",
     *                 type="array",
     *                 @SWG\Items(
     *                   @SWG\Property(property="name", type="string"),
     *                   @SWG\Property(property="description", type="string"),
     *                   @SWG\Property(property="amount", type="integer"),
     *                   @SWG\Property(property="currency", type="string"),
     *                   @SWG\Property(property="quantity", type="integer"),
     *                   @SWG\Property(property="tax", type="number"),
     *                 ),
     *               ),
     *            ),
     *          )
     *      ),
     *      @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *      @SWG\Response(
     *          response=403,
     *          description="Permission Denied"
     *      ),
     *      @SWG\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *      @SWG\Response(
     *          response=404,
     *          description="Item not found"
     *      ),
     *      @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function store(InvoiceCreateRequest $request, Team $team)
    {
        $this->authorize('member', $team);

        $invoice = $this->invoiceRepository->create($request->all(), $team);

        return (new InvoiceResource($invoice))->response()->setStatusCode(200);
    }

    /**
     * Get invoice
     *
     * @param Team $team
     * @param Invoice  $invoice
     * @return InvoiceResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Get(
     *      path="/teams/{team_id}/invoices/{invoice_id}",
     *      tags={"Invoices"},
     *      summary="Get invoice",
     *      operationId="api.invoices.get",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          type="string",
     *          default="Bearer <token>",
     *          description="Authorization"
     *      ),
     *     @SWG\Parameter(
     *          name="team_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *     @SWG\Parameter(
     *          name="invoice_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="id", type="string"),
     *            @SWG\Property(property="invoice_number", type="string"),
     *            @SWG\Property(property="message", type="string"),
     *            @SWG\Property(property="status", type="string"),
     *            @SWG\Property(property="platform", type="string"),
     *            @SWG\Property(property="application_fee", type="number", description="application fee in US cents"),
     *            @SWG\Property(property="receipt_pdf", type="string"),
     *            @SWG\Property(property="paid_at", type="string", format="date-time"),
     *            @SWG\Property(property="refunded_at", type="string", format="date-time"),
     *            @SWG\Property(property="created_at", type="string", format="date-time"),
     *            @SWG\Property(property="updated_at", type="string", format="date-time"),
     *            @SWG\Property(
     *              property="payment_provider",
     *              type="object",
     *                 @SWG\Property(property="provider", type="string"),
     *                 @SWG\Property(property="public_key", type="string"),
     *                 @SWG\Property(property="currency", type="string"),
     *            ),
     *            @SWG\Property(
     *              property="order",
     *              type="object",
     *                 @SWG\Property(property="shipping", type="boolean"),
     *                 @SWG\Property(property="status", type="string"),
     *                 @SWG\Property(property="tracking_company", type="string"),
     *                 @SWG\Property(property="tracking_number", type="string"),
     *                 @SWG\Property(property="tracking_url", type="string"),
     *                 @SWG\Property(property="fulfilled_at", type="string", format="date-time"),
     *            ),
     *            @SWG\Property(
     *              property="contact",
     *              type="object",
     *                 @SWG\Property(property="id", type="string"),
     *                 @SWG\Property(property="phone", type="string"),
     *                 @SWG\Property(property="platform", type="string"),
     *                 @SWG\Property(property="name", type="string"),
     *                 @SWG\Property(property="legal_name", type="string"),
     *                 @SWG\Property(property="avatar", type="string"),
     *                 @SWG\Property(
     *                      property="address",
     *                      type="object",
     *                      @SWG\Property(property="city", type="string"),
     *                      @SWG\Property(property="line", type="string"),
     *                      @SWG\Property(property="postal_code", type="string"),
     *                      @SWG\Property(property="state", type="string"),
     *                      @SWG\Property(
     *                          property="country",
     *                          type="object",
     *                          @SWG\Property(property="name", type="string"),
     *                          @SWG\Property(property="code", type="string"),
     *                      ),
     *                 ),
     *            ),
     *            @SWG\Property(
     *              property="purchase_summary",
     *              type="object",
     *              @SWG\Property(property="shipping", type="boolean"),
     *              @SWG\Property(property="shipping_cost", type="number"),
     *              @SWG\Property(property="total", type="number"),
     *              @SWG\Property(property="subtotal", type="number"),
     *              @SWG\Property(property="currency", type="string"),
     *              @SWG\Property(property="tax", type="number"),
     *              @SWG\Property(property="refund_balance", type="number"),
     *              @SWG\Property(
     *                 property="items",
     *                 type="array",
     *                 @SWG\Items(
     *                   @SWG\Property(property="name", type="string"),
     *                   @SWG\Property(property="description", type="string"),
     *                   @SWG\Property(property="amount", type="integer"),
     *                   @SWG\Property(property="currency", type="string"),
     *                   @SWG\Property(property="quantity", type="integer"),
     *                   @SWG\Property(property="tax", type="number"),
     *                 ),
     *               ),
     *            ),
     *          )
     *      ),
     *     @SWG\Response(
     *          response=403,
     *          description="Permission Denied"
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *     @SWG\Response(
     *          response=404,
     *          description="Item not found"
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function show(Team $team, Invoice $invoice)
    {
        $this->authorize('viewInvoices', $team);

        return new InvoiceResource($invoice);
    }

    /**
     * Get invoice public data
     *
     * @param Invoice $invoice
     * @return InvoicePublicResource
     *
     * @SWG\Get(
     *      path="/invoices/{invoice_id}/public",
     *      tags={"Invoices"},
     *      summary="Get invoice public data",
     *      operationId="api.invoices.public",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="invoice_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="id", type="string"),
     *            @SWG\Property(property="invoice_number", type="string"),
     *            @SWG\Property(property="status", type="string"),
     *            @SWG\Property(
     *              property="payment_provider",
     *              type="object",
     *                 @SWG\Property(property="provider", type="string"),
     *                 @SWG\Property(property="public_key", type="string"),
     *                 @SWG\Property(property="currency", type="string"),
     *            ),
     *            @SWG\Property(
     *              property="purchase_summary",
     *              type="object",
     *              @SWG\Property(property="shipping", type="boolean"),
     *              @SWG\Property(property="shipping_cost", type="number"),
     *              @SWG\Property(property="total", type="number"),
     *              @SWG\Property(property="subtotal", type="number"),
     *              @SWG\Property(property="currency", type="string"),
     *              @SWG\Property(property="tax", type="number"),
     *              @SWG\Property(
     *                 property="items",
     *                 type="array",
     *                 @SWG\Items(
     *                   @SWG\Property(property="name", type="string"),
     *                   @SWG\Property(property="description", type="string"),
     *                   @SWG\Property(property="amount", type="integer"),
     *                   @SWG\Property(property="currency", type="string"),
     *                   @SWG\Property(property="quantity", type="integer"),
     *                   @SWG\Property(property="tax", type="number"),
     *                 ),
     *               ),
     *            ),
     *          )
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *     @SWG\Response(
     *          response=404,
     *          description="Item not found"
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function showPublic(Invoice $invoice)
    {
        return new InvoicePublicResource($invoice);
    }

    /**
     * Get next invoice number
     *
     * @param Team $team
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Get(
     *      path="/teams/{team_id}/invoices/next-invoice-number",
     *      tags={"Invoices"},
     *      summary="Get next invoice number",
     *      operationId="api.invoices.invoice_number",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          type="string",
     *          default="Bearer <token>",
     *          description="Authorization"
     *      ),
     *      @SWG\Parameter(
     *          name="team_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            @SWG\Property(property="invoice_number", type="string")
     *          )
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *     @SWG\Response(
     *          response=403,
     *          description="Permission Denied"
     *      ),
     *     @SWG\Response(
     *          response=404,
     *          description="Item not found"
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function nextInvoiceNumber(Team $team)
    {
        $this->authorize('member', $team);

        $invoiceNumber = $this->invoiceRepository->getNextInvoiceNumber();

        return response()->json(['invoice_number' => $invoiceNumber]);
    }

    /**
     * @param Team $team
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getMonthRevenue(Team $team)
    {
        // todo: remove to separate controller once we add statistics
        $this->authorize('viewInvoices', $team);

        $monthStart = Carbon::now()->startOfMonth();
        $now = Carbon::now();

        $invoices = $team->invoices()
            ->where('created_at', '>=', $monthStart)
            ->where('created_at', '<=', $now)
            ->whereIn('status', ['paid', 'partially_refunded'])
            ->with('items')->get();

        $totalAmount = $invoices->sum('total') - $invoices->sum('refunded_amount');

        return response()->json(['total_revenue' => $totalAmount]);
    }

    /**
     * Delete invoice
     *
     * @param Team $team
     * @param Invoice  $invoice
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @SWG\Delete(
     *      path="/teams/{team_id}/invoices/{invoice_id}",
     *      tags={"Invoices"},
     *      summary="Delete invoice",
     *      operationId="api.invoices.delete",
     *      produces={"application/json"},
     *     @SWG\Parameter(
     *          name="Authorization",
     *          in="header",
     *          required=true,
     *          type="string",
     *          default="Bearer <token>",
     *          description="Authorization"
     *      ),
     *     @SWG\Parameter(
     *          name="team_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *     @SWG\Parameter(
     *          name="invoice_id",
     *          in="path",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Success",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      ),
     *     @SWG\Response(
     *          response=403,
     *          description="Permission Denied"
     *      ),
     *     @SWG\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *     @SWG\Response(
     *          response=404,
     *          description="Item not found"
     *      ),
     *     @SWG\Response(
     *          response=500,
     *          description="Internal Server Error",
     *          @SWG\Schema(
     *            type="object",
     *            @SWG\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function delete(Team $team, Invoice $invoice)
    {
        $this->authorize('manageInvoices', $team);

        $invoice->delete();

        return response()->json(['message' => 'Invoice has been successfully deleted.']);
    }
}
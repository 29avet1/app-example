<?php namespace App;

use App\Traits\Models\HasChildConnection;
use App\Traits\Models\UsesUuids;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laratrust\Traits\LaratrustTeamTrait;

/**
 * Class Teams
 * @property integer                 $id
 * @property string                  $uuid
 * @property integer                 $user_id
 * @property string                  $name
 * @property string                  $website
 * @property string                  $logo
 * @property string                  $email
 * @property string                  $slug
 * @property string                  $address
 * @property string                  $about
 * @property string                  $description
 * @property Carbon                  $created_at
 * @property Carbon                  $updated_at
 * @property User                    $owner
 * @property Collection              $users
 * @property Collection              $invoices
 * @property Collection              $contacts
 * @property Collection              $invitations
 * @property Collection              $webhook_endpoints
 * @property Collection              $selected_webhooks
 * @package App
 * @mixin \Eloquent
 */
class Team extends Model
{
    use UsesUuids, SoftDeletes, LaratrustTeamTrait, HasChildConnection;

    protected $connection = 'pgsql';
    protected $table = 'teams';

    protected $fillable = [
        'uuid',
        'user_id',
        'slug',
        'name',
        'legal_team_name',
        'legal_entity_type',
        'website',
        'logo',
        'email',
        'address',
        'about',
        'description',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'connection_name',
        'schema_name',
        'plan',
        'whatsapp_identifier',
        'whatsapp_template_namespace',
    ];

    //-----Attributes-------------------------------------------------------------------------------------------------//

    /**
     * @param $logo
     * @return string
     */
    public function getLogoAttribute($logo)
    {
        return $logo ? config('filesystems.disks.s3_public.domain') . "/team_{$this->uid}/logo/{$logo}" : '';
    }


    //-----Boolean Methods--------------------------------------------------------------------------------------------//

    /**
     * @return bool
     */
    public function hasOnlineUsers()
    {
        return !!$this->users()->where('users.online', true)->count();
    }

    /**
     * @return bool
     */
    public function hasWebhooksEnabled()
    {
        return !!$this->webhook_endpoints()->where('active', true)->count();
    }

    //-----Methods----------------------------------------------------------------------------------------------------//

    /**
     * @param User|null $user
     * @return Role|null
     */
    public function getUserRole(User $user = null)
    {
        $user = $user ?? auth()->user();

        return $user->roles()->where('role_user.team_id', $this->id)->first();
    }

    //-----Relations--------------------------------------------------------------------------------------------------//

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'team_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'team_id', 'role_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'team_id', 'permission_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'team_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'team_id', 'id')
            ->where('contacted', true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'team_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function webhook_endpoints()
    {
        return $this->hasMany(WebhookEndpoint::class, 'team_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function selected_webhooks()
    {
        return $this->hasMany(SelectedWebhook::class, 'team_id', 'id');
    }
}
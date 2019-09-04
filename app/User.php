<?php namespace App;

use App\Notifications\ResetPasswordNotification;
use App\Traits\Models\RolesUserTrait;
use App\Traits\Models\UsesUuids;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 * @property integer     $id
 * @property string      $uid
 * @property string      $name
 * @property string      $first_name
 * @property string      $last_name
 * @property string      $email
 * @property string      $avatar
 * @property string      $password
 * @property boolean     $online
 * @property boolean     $available
 * @property string      $remember_token
 * @property Carbon      $created_at
 * @property Carbon      $updated_at
 * @property Collection  $teams
 * @property Collection  $invitations
 * @mixin \Eloquent
 * @package App
 */
class User extends Authenticatable
{
    use Notifiable, UsesUuids, RolesUserTrait, HasApiTokens;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid',
        'first_name',
        'last_name',
        'email',
        'avatar',
        'password',
        'online',
        'available',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'uuid',
        'password',
        'remember_token',
    ];

    protected $appends = [
        'name'
    ];

    //-----Attributes-------------------------------------------------------------------------------------------------//

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @param $avatar
     * @return string
     */
    public function getAvatarAttribute($avatar)
    {
        return $avatar ? config('filesystems.disks.s3_public.domain') . "/user_{$this->uid}/avatars/{$avatar}" : '';
    }

    //-----Methods----------------------------------------------------------------------------------------------------//

    /**
     * @param $password
     * @throws AuthenticationException
     */
    public function checkPassword($password)
    {
        if (!Hash::check($password, $this->getAuthPassword())) {
            throw new AuthenticationException('Authentication failed. Wrong Password.');
        };
    }

    /**
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * @param Team $team
     * @param bool     $query
     * @return Role|null
     */
    public function roleInTeam(Team $team, $query = true)
    {
        $roles = $query ? $this->roles() : $this->roles;
        $relation = $query ? 'role_user' : 'pivot';

        return $roles->where("{$relation}.team_id", $team->id)->first();
    }


    //-----Relations--------------------------------------------------------------------------------------------------//

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function owned_teams()
    {
        return $this->hasMany(Team::class, 'user_id', 'id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'role_user', 'user_id', 'team_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id')
            ->withPivot('team_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'user_id', 'id');
    }
}

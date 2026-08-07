<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = "users";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nip',
        'name',
        'email',
        'password',
        'image',
        'sign',
        'birthday',
        'address',
        'area',
        'code',
        'active',
        'role',
        'date_in',
        'remember_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function detail()
    {
        return $this->hasMany('App\Models\DetailUser', 'id_users');
    }

    public function clients()
    {
        return $this->hasMany('App\Models\Client', 'id_sales');
    }

    public function quotation()
    {
        return $this->hasMany('App\Models\Quotation', 'id_sales');
    }
    public function target()
    {
        return $this->hasMany('App\Models\Target', 'id_sales');
    }
    public function audit()
    {
        return $this->hasMany('App\Models\Audit', 'id_technician');
    }
    public function toolsAssigned()
    {
        return $this->hasMany('App\Models\FixedAsset', 'id_pic')->where('type', 'Tools');
    }
    public function toolAssignmentEntry()
    {
        return $this->hasOne('App\Models\ToolAssignmentTechnician', 'user_id');
    }
    public function product_out()
    {
        return $this->hasMany('App\Models\ProductOut', 'id_user');
    }
    public function status()
    {
        return $this->hasMany('App\Models\ChangeStatus', 'id_user');
    }
    public function monitoring()
    {
        return $this->hasMany('App\Models\Monitoring', 'id_user');
    }
    public function monitoringStatus()
    {
        return $this->hasMany('App\Models\StatusMonitoring', 'id_pic');
    }
    public function salesOnline()
    {
        return $this->hasMany('App\Models\SalesOnline', 'id_sales');
    }
    public function opname()
    {
        return $this->hasMany('App\Models\StockOpname', 'id_user');
    }

    public function prospects()
    {
        return $this->hasMany(Prospect::class, 'id_sales');
    }

    public function latestTarget()
    {
        return $this->hasOne(Target::class, 'id_sales')->latestOfMany();
    }
    public function latestRole()
    {
        return $this->hasOne(DetailUser::class, 'id_users')->latestOfMany();
    }

    public function kanbanBoards()
    {
        return $this->belongsToMany(KanbanBoard::class, 'kanban_board_members', 'user_id', 'board_id');
    }

    public function handledSales()
    {
        return $this->belongsToMany(User::class, 'accounting_sales_mapping', 'id_accounting', 'id_sales');
    }
}

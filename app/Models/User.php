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
        'banner',
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


    /**
     * Developer accounts see the same data/pages as Admin everywhere in the app.
     * The raw DB value stays 'Developer' so Developer-only features (maintenance
     * tools, nav badges) can still tell the two apart via isDeveloper().
     */
    public function getRoleAttribute($value)
    {
        return $value === 'Developer' ? 'Admin' : $value;
    }

    public function isDeveloper(): bool
    {
        return $this->getRawOriginal('role') === 'Developer';
    }

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

    /**
     * Dapatkan daftar ID user Accounting yang menangani sales tertentu berdasarkan
     * tabel accounting_sales_mapping. Jika includeAdmin true, sertakan juga Admin.
     * Jika sales belum dimapping ke siapapun, fallback ke semua user Accounting aktif.
     */
    public static function getAccountingRecipientsForSales(?int $salesId, bool $includeAdmin = true): array
    {
        $accountingIds = [];
        if ($salesId) {
            $accountingIds = \Illuminate\Support\Facades\DB::table('accounting_sales_mapping')
                ->where('id_sales', $salesId)
                ->pluck('id_accounting')
                ->toArray();
        }

        // Fallback jika belum dimapping ke accounting manapun: kirim ke semua Accounting aktif
        if (empty($accountingIds)) {
            $accountingIds = self::where('role', 'Accounting')->where('active', '1')->pluck('id')->toArray();
        }

        if ($includeAdmin) {
            $adminIds = self::where('role', 'Admin')->where('active', '1')->pluck('id')->toArray();
            $accountingIds = array_merge($accountingIds, $adminIds);
        }

        return array_values(array_unique(array_filter($accountingIds)));
    }

    public function sentMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(ChatMessage::class, 'receiver_id');
    }

    /**
     * User aktif yang perlu muncul di tabel/leaderboard per-sales — role Sales
     * beneran (satu baris per orang, `id_sales_list` = [id sendiri]), PLUS semua
     * role Admin yang ternyata pernah bikin quotation sendiri (kayak Regita)
     * DIGABUNG jadi SATU baris "Sales Project" (`id_sales_list` = id semua Admin
     * itu). Konsumennya query pakai whereIn('id_sales', $user->id_sales_list)
     * bukan where('id_sales', $user->id), biar yang gabungan ikut ke-agregat benar.
     */
    public static function activeSalesAndProjectAdmins()
    {
        $adminAuthorIds = \Illuminate\Support\Facades\DB::table('quotation')
            ->whereNotNull('id_sales')
            ->pluck('id_sales')
            ->merge(
                \Illuminate\Support\Facades\DB::table('unit_quotation')
                    ->whereNotNull('id_sales')
                    ->pluck('id_sales')
            )
            ->unique();

        $salesUsers = static::where('role', 'Sales')->where('active', '1')->get();
        $salesUsers->each(function ($user) {
            $user->id_sales_list = [$user->id];
        });

        $projectAdmins = static::where('role', 'Admin')
            ->where('active', '1')
            ->whereIn('id', $adminAuthorIds)
            ->get();

        if ($projectAdmins->isNotEmpty()) {
            $projectRow = clone $projectAdmins->first();
            $projectRow->name = 'Sales Project';
            $projectRow->id_sales_list = $projectAdmins->pluck('id')->all();
            $salesUsers->push($projectRow);
        }

        return $salesUsers;
    }

    public function mailSetting()
    {
        return $this->hasOne(\App\Models\UserMailSetting::class, 'user_id');
    }

    public function mailboxMessages()
    {
        return $this->hasMany(\App\Models\MailboxMessage::class, 'user_id');
    }
}

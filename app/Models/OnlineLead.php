<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OnlineLead extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'online_leads';

    protected $fillable = [
        'id_sales',
        'id_client',
        'id_quotation',
        'id_unit_quotation',
        'channel',
        'customer_type',
        'name',
        'phone',
        'company',
        'product_interest',
        'notes',
        'status',
        'date',
        'converted_at',
    ];

    protected $casts = [
        'date' => 'date',
        'converted_at' => 'datetime',
    ];

    public const CHANNELS = [
        'Tokopedia - Airend Center' => [
            'entity' => 'Reftech',
            'badge' => 'success',
            'icon' => 'mdi-shopping-outline',
        ],
        'Tokopedia - Part Compressor' => [
            'entity' => 'Kojisha',
            'badge' => 'success',
            'icon' => 'mdi-shopping-outline',
        ],
        'Tokopedia - Kojisha Filter' => [
            'entity' => 'Kojisha',
            'badge' => 'success',
            'icon' => 'mdi-shopping-outline',
        ],
        'Shopee - Kojisha Filter' => [
            'entity' => 'Kojisha',
            'badge' => 'warning',
            'icon' => 'mdi-shopping',
        ],
        'WhatsApp - Airend Center' => [
            'entity' => 'Reftech',
            'badge' => 'info',
            'icon' => 'mdi-whatsapp',
        ],
        'WhatsApp - Part Compressor' => [
            'entity' => 'Kojisha',
            'badge' => 'info',
            'icon' => 'mdi-whatsapp',
        ],
        'WhatsApp - Kojisha' => [
            'entity' => 'Kojisha',
            'badge' => 'info',
            'icon' => 'mdi-whatsapp',
        ],
        'Indotrading' => [
            'entity' => 'General',
            'badge' => 'primary',
            'icon' => 'mdi-domain',
        ],
    ];

    public const STATUSES = [
        'new' => ['label' => 'New Chat', 'badge' => 'secondary'],
        'contacted' => ['label' => 'Follow-up', 'badge' => 'info'],
        'quoted' => ['label' => 'Quoted', 'badge' => 'primary'],
        'deal' => ['label' => 'Deal / PO', 'badge' => 'success'],
        'lost' => ['label' => 'Lost / Batal', 'badge' => 'danger'],
    ];

    public function sales()
    {
        return $this->belongsTo(User::class, 'id_sales');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'id_quotation');
    }

    public function unitQuotation()
    {
        return $this->belongsTo(UnitQuotation::class, 'id_unit_quotation');
    }

    /**
     * Histori follow-up & kebutuhan lead ini dari waktu ke waktu — tiap follow-up
     * baru dicatat sebagai baris baru (bukan nimpa product_interest/notes),
     * jadi kebutuhan lead ini dari dulu sampai sekarang tetap kelihatan semua.
     */
    public function followUps()
    {
        return $this->hasMany(OnlineLeadFollowUp::class, 'id_lead')->with('items')->latest('date')->latest('id');
    }

    public function scopeVisibleTo($query, ?User $user = null)
    {
        $user = $user ?? Auth::user();
        if (!$user) {
            return $query;
        }

        $canViewAll = in_array($user->role, ['Admin', 'Developer', 'Sales Manager'])
            || (method_exists($user, 'isDeveloper') && $user->isDeveloper());

        if (!$canViewAll) {
            $query->where('id_sales', $user->id);
        }

        return $query;
    }

    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
                ->orWhere('company', 'LIKE', "%{$term}%")
                ->orWhere('phone', 'LIKE', "%{$term}%")
                ->orWhere('product_interest', 'LIKE', "%{$term}%")
                ->orWhere('notes', 'LIKE', "%{$term}%");
        });
    }

    public function getCleanPhoneAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', (string) $this->phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }

    public function getWaUrlAttribute(): ?string
    {
        $clean = $this->clean_phone;
        if (empty($clean)) {
            return null;
        }
        $greeting = urlencode("Halo " . ($this->name ?: 'Kak') . ", terima kasih sudah menghubungi kami. Terkait inquiry " . ($this->product_interest ?: 'produk') . "...");
        return "https://wa.me/{$clean}?text={$greeting}";
    }

    /**
     * Channel dari marketplace (Tokopedia/Shopee) sering gak nampilin no. HP
     * pembeli sebelum di-follow-up manual, jadi phone gak wajib buat channel ini.
     */
    public static function isMarketplaceChannel(?string $channel): bool
    {
        if (empty($channel)) {
            return false;
        }
        return str_contains($channel, 'Tokopedia') || str_contains($channel, 'Shopee');
    }

    public function getChannelMetaAttribute(): array
    {
        return self::CHANNELS[$this->channel] ?? [
            'entity' => 'Unknown',
            'badge' => 'secondary',
            'icon' => 'mdi-chat-processing-outline',
        ];
    }

    public function getStatusMetaAttribute(): array
    {
        return self::STATUSES[$this->status] ?? [
            'label' => ucfirst($this->status),
            'badge' => 'secondary',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceUsages extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'service_name',
        'period_start',
        'request_count',
        'images_generated'
    ];
    
    protected $casts = [
        'period_start' => 'date',
    ];
    
    /**
     * Obtener el usuario al que pertenece este registro de uso
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Incrementar el contador de solicitudes para un usuario y servicio
     */
    public static function incrementRequestCount(int $userId, string $serviceName, int $increment = 1): self
    {
        // Obtener el periodo actual (primer día del mes)
        $periodStart = now()->startOfMonth()->format('Y-m-d');
        
        // Buscar o crear registro de uso para este mes
        $usage = self::firstOrNew([
            'user_id' => $userId,
            'service_name' => $serviceName,
            'period_start' => $periodStart,
        ]);
        
        // Incrementar contador de solicitudes
        $usage->request_count = ($usage->request_count ?? 0) + $increment;
        $usage->save();
        
        return $usage;
    }
    
    /**
     * Verificar si un usuario ha alcanzado su límite mensual para un servicio
     */
    // public static function hasReachedLimit(int $userId, string $serviceName): bool
    // {
    //     // Obtener el usuario
    //     $user = User::find($userId);
    //     if (!$user) return true; // Si no hay usuario, consideramos que ha alcanzado el límite
        
    //     // Obtener la suscripción del usuario (sin verificar si está activa)
    //     $subscription = Subscription::where('user_id', $userId)->first();
        
    //     if (!$subscription) return false; // Si no hay suscripción, asumimos que no hay límite
        
    //     // Buscar límite para este servicio en el plan
    //     $limit = PlanServiceLimits::where('plan_id', $subscription->plan_id)
    //         ->where('service_name', $serviceName)
    //         ->first();
        
    //     if (!$limit) return true; // Si no hay límite definido, permitimos el uso en false negamos en true
        
    //     // Obtener uso actual de este mes
    //     $periodStart = now()->startOfMonth()->format('Y-m-d');
    //     $currentUsage = self::where('user_id', $userId)
    //         ->where('service_name', $serviceName)
    //         ->where('period_start', $periodStart)
    //         ->first();
        
    //     // Verificar si ha alcanzado el límite
    //     if ($currentUsage && $currentUsage->request_count >= $limit->monthly_limit) {
    //         return true; // Ha alcanzado o superado el límite
    //     }
        
    //     return false; // No ha alcanzado el límite
    // }
 public static function hasReachedLimit(int $userId, string $serviceName): bool
{
    try {
        // Obtener el usuario
        $user = User::find($userId);
        if (!$user) {
            \Log::error('Usuario no encontrado en hasReachedLimit', ['userId' => $userId]);
            return true;
        }

        // Verificar si es admin
        $isAdmin = $user->roles()->where('name', 'Super Admin')->exists();
        \Log::info('Verificación de admin', ['userId' => $userId, 'isAdmin' => $isAdmin]);

        if ($isAdmin) {
            return false;
        }

        // Verificar suscripción
        $subscription = Subscription::where('user_id', $userId)->first();
        \Log::info('Verificación de suscripción', ['userId' => $userId, 'hasSubscription' => !is_null($subscription)]);

        if (!$subscription) {
            return true;
        }

        // Verificar límite del servicio
        $limit = PlanServiceLimits::where('plan_id', $subscription->plan_id)
            ->where('service_name', $serviceName)
            ->first();
        \Log::info('Verificación de límite', [
            'userId' => $userId,
            'serviceName' => $serviceName,
            'planId' => $subscription->plan_id,
            'hasLimit' => !is_null($limit)
        ]);

        if (!$limit) {
            return true;
        }

        // Verificar uso actual
        $periodStart = now()->startOfMonth()->format('Y-m-d');
        $currentUsage = self::where('user_id', $userId)
            ->where('service_name', $serviceName)
            ->where('period_start', $periodStart)
            ->first();

        $usageCount = $currentUsage ? $currentUsage->request_count : 0;
        \Log::info('Verificación de uso actual', [
            'userId' => $userId,
            'serviceName' => $serviceName,
            'currentUsage' => $usageCount,
            'limit' => $limit->monthly_limit
        ]);

        return $usageCount >= $limit->monthly_limit;

    } catch (\Exception $e) {
        \Log::error('Error en hasReachedLimit', [
            'userId' => $userId,
            'serviceName' => $serviceName,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return true;
    }
}
    
    /**
     * Obtener el límite mensual para un servicio y usuario
     */
    public static function getMonthlyLimit(int $userId, string $serviceName): ?int
    {
        // Obtener la suscripción del usuario (sin verificar si está activa)
        $subscription = Subscription::where('user_id', $userId)->first();
        
        if (!$subscription) return null;
        
        // Buscar límite para este servicio en el plan
        $limit = PlanServiceLimits::where('plan_id', $subscription->plan_id)
            ->where('service_name', $serviceName)
            ->first();
        
        return $limit ? $limit->monthly_limit : null;
    }
    
    /**
     * Obtener uso actual para un servicio y usuario en el mes actual
     */
    public static function getCurrentUsage(int $userId, string $serviceName): int
    {
        $periodStart = now()->startOfMonth()->format('Y-m-d');
        $usage = self::where('user_id', $userId)
            ->where('service_name', $serviceName)
            ->where('period_start', $periodStart)
            ->first();
            
        return $usage ? $usage->request_count : 0;
    }
}
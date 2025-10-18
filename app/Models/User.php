<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\QueryException;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Field;
use App\Models\Topic;
use DB;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, Billable;

    const ROLE_FREE = 'free';
    const ROLE_START = 'start';
    const ROLE_PRO = 'pro';
    const ROLE_PREMIUM = 'premium';
    const ROLE_ADMIN = 'admin';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_PAST_DUE = 'past_due';
    const STATUS_UNPAID = 'unpaid';
    const STATUS_INCOMPLETE = 'incomplete';
    const STATUS_TRIAL = 'trial';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'name',
        'surname',
        'password',
        'avatar',
        'email_verified_at',
        'plan_expires_at',
        'timezone',
        'language',
        'phone',
        'status',
        'global_key_api',
        'email_verification_code',
        'email_verification_sent_at',
        'email_verified',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'email_verification_sent_at' => 'datetime',
            'plan_expires_at' => 'datetime',
            'password' => 'hashed',
            'email_verified' => 'boolean',
            'last_login_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims() {
        return [];
    }

    public function collaborations()
    {
        return $this->hasMany(Collaborator::class, 'user_id')
                    ->where('status', 'accepted')
                    ->with('workspace');
    }

    public function allCollaborations()
    {
        return $this->hasMany(Collaborator::class, 'user_id')
                    ->with('workspace');
    }

    public function pendingCollaborations()
    {
        return $this->hasMany(Collaborator::class, 'user_id')
                    ->where('status', 'pending')
                    ->with('workspace');
    }
    
    public function workspaces(){
        return $this->hasMany(Workspace::class);
    }

    public function fields()
    {
        return Field::whereHas('topic.workspace', function($query) {
            $query->where('user_id', $this->id);
        });
    }
    
    public function topics()
    {
        return $this->hasManyThrough(Topic::class, Workspace::class);
    }

    public function planInfo()
    {
        $plan = $this->getPlan();
        
        return [
            'plan' => $plan,
            'role' => $this->getRoleNames()->first(),
            'limits' => [
                'max_workspaces' => $plan->max_workspaces,
                'max_topics' => $plan->max_topics,
                'max_fields' => $plan->max_fields,
                'can_export' => $plan->can_export,
                'can_use_api' => $plan->can_use_api,
            ],
            'is_admin' => $this->isAdmin(),
            'is_premium' => $this->isPremium(),
            'is_pro' => $this->isPro(),
            'is_start' => $this->isStart(),
            'is_free' => $this->isFree()
        ];
    }

    /**
     * Verifica se o email foi verificado
     */
    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified;
    }

    /**
     * Gera um código de verificação
     */
    public function generateVerificationCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verifica se usuário é ADMIN
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Verificar se usuário é PREMIUM
     */
    public function isPremium(): bool
    {
        return $this->hasRole(self::ROLE_PREMIUM);
    }

    /**
     * Verifica se usuário é PRO
     */
    public function isPro(): bool
    {
        return $this->hasRole(self::ROLE_PRO);
    }

    /**
     * Verificar se usuário é START
     */
    public function isStart(): bool
    {
        return $this->hasRole(self::ROLE_START);
    }

    /**
     * Verifica se usuário é FREE
     */
    public function isFree(): bool
    {
        return $this->hasRole(self::ROLE_FREE);
    }

    /**
     * Consulta todos os usuário
     */
    public function getAllUsers() {
        try{
            return User::select(
                'users.id',
                'users.email',
                'users.name',
                'users.surname',
                'users.status',
                'roles.name as plan_name',
                'plans.max_workspaces',
                'plans.max_topics',
                'plans.max_fields',
                'plans.can_export',
                'plans.can_use_api',
                'plans.is_active'
            )
            ->leftJoin('model_has_roles', ['model_has_roles.model_id' => 'users.id'])
            ->leftJoin('roles', ['roles.id' => 'model_has_roles.role_id'])
            ->leftJoin('plans', ['plans.name' => 'roles.name'])
            ->where('model_has_roles.model_type', User::class)
            ->get();
        }catch(\QueryException $e){
            dd($e->error());
        }
    }

    public function getUserById($userId = null): object
    {
        try{
            $user = DB::table('users')
            ->leftJoin('model_has_roles', function($join) use ($userId) {
                $join->on(['model_has_roles.model_id' => 'users.id'])
                    ->where('model_has_roles.model_type', 'App\Models\User');
            })
            ->leftJoin('roles', ['roles.id' => 'model_has_roles.role_id'])
            ->leftJoin('plans', ['roles.name' => 'plans.name'])
            ->select(
                'users.id',
                'users.email',
                'users.name',
                'users.surname',
                'roles.name as plan_name',
                'plans.max_workspaces',
                'plans.max_topics',
                'plans.max_fields',
                'plans.can_export',
                'plans.is_active')
            ->where(['users.id' => $userId])->first();
            return $user;
        }catch(\QueryException $e){
            dd($e->error());
        }
    }
    
    // Verificar limites do plano COM SEGURANÇA
    public function canCreateWorkspace(): bool
    {
        if ($this->isAdmin() || $this->isPremium()) return true;
        
        // Usuários com problemas de pagamento podem ter acesso restrito
        if ($this->hasPaymentIssues()) {
            // Permitir acesso básico mas mostrar alertas
            return true;
        }
        
        $plan = $this->getPlan();
        $currentWorkspaces = $this->workspaces()->count();
        
        return $plan->max_workspaces === 0 || $currentWorkspaces < $plan->max_workspaces;
    }
    
    public function canAddMoreFields($workspaceId = null, $topicId = null): bool
    {
        // Admin, Premium e Pro sempre podem (planos ilimitados)
        if ($this->isAdmin() || $this->isPremium() || $this->isPro()) {
            return true;
        }
        
        $plan = $this->getPlan();
        
        // Se for plano ilimitado, sempre pode adicionar
        if ($plan->max_fields === 0) {
            return true;
        }
        
        $currentCount = $this->getCurrentFieldsCount($workspaceId, $topicId);
        $canAdd = $currentCount < $plan->max_fields;
        
        return $canAdd;
    }

    /**
     * Obter limite de campos do plano
     */
    public function getFieldsLimit(): int
    {
        $plan = $this->getPlan();
        
        if ($this->isPro() || $this->isPremium() || $this->isAdmin()) {
            return 0;
        }
        
        return $plan->max_fields;
    }

    /**
     * Obter número atual de campos POR TÓPICO (MÉTODO ALTERNATIVO)
     */
    public function getTopicFieldsCount($topicId): int
    {
        if (!$topicId) {
            return 0;
        }
        
        try {
            // Buscar o tópico e verificar se pertence ao usuário
            $topic = Topic::with('workspace')->find($topicId);
            
            if (!$topic) {
                \Log::warning("Tópico não encontrado", ['topic_id' => $topicId]);
                return 0;
            }
            
            // Verificar se o usuário é owner ou colaborador
            $isOwner = $topic->workspace->user_id === $this->id;
            $isCollaborator = $topic->workspace->collaborators()
                ->where('user_id', $this->id)
                ->where('status', 'accepted')
                ->exists();
                
            if (!$isOwner && !$isCollaborator) {
                \Log::warning("Usuário não tem acesso ao tópico", [
                    'user_id' => $this->id,
                    'topic_id' => $topicId,
                    'workspace_owner' => $topic->workspace->user_id
                ]);
                return 0;
            }
            
            // Contar apenas campos deste tópico específico
            $count = Field::where('topic_id', $topicId)->count();
            
            return $count;
        } catch (\Exception $e) {
            \Log::error('Erro em getTopicFieldsCount: ' . $e->getMessage());
            return 0;
        }
    }


    /**
     * Obter limite de campos POR TÓPICO
     */
    public function getFieldsLimitPerTopic(): int
    {
        $plan = $this->getPlan();
        
        // Planos pro, premium e admin têm limite ilimitado por tópico
        if ($this->isPro() || $this->isPremium() || $this->isAdmin()) {
            return 0;
        }
        
        return $plan->max_fields; // Retorna o limite por tópico (ex: 10)
    }

    /**
     * Verificar se o plano é ilimitado
     */
    public function hasUnlimitedFields(): bool
    {
        if ($this->isAdmin() || $this->isPremium() || $this->isPro()) {
            return true;
        }
        
        $plan = $this->getPlan();
        return $plan->max_fields === 0;
    }


    public function getCurrentFieldsCount($workspaceId = null, $topicId = null): int
    {
        $query = Field::query();
        
        //Sempre filtrar pelo usuário dono do workspace
        $query->whereHas('topic.workspace', function($q) {
            $q->where('user_id', $this->id);
        });

        if ($topicId) {
            //Contar APENAS campos do tópico específico
            $query->where('topic_id', $topicId);
        } elseif ($workspaceId) {
            // Contar apenas campos do workspace específico
            $query->whereHas('topic', function($q) use ($workspaceId) {
                $q->where('workspace_id', $workspaceId);
            });
        }
        
        $count = $query->count();
        
        // Log para verificar exatamente o que está sendo contado
        \Log::info("🔍 getCurrentFieldsCount", [
            'user_id' => $this->id,
            'workspace_id' => $workspaceId,
            'topic_id' => $topicId,
            'count' => $count,
            'query_topic_id' => $topicId ? "FILTERED by topic_id: $topicId" : "NOT FILTERED by topic",
            'actual_topic_fields' => $topicId ? Field::where('topic_id', $topicId)->count() : 'N/A'
        ]);
        
        return $count;
    }

    public function canExportData(): bool
    {
        $plan = $this->getPlan();
        return $plan->can_export || $this->isAdmin();
    }

    public function canUseApi(): bool
    {
        $plan = $this->getPlan();
        return $plan->can_use_api || $this->isAdmin();
    }

    // Accessors para acesso seguro aos dados do plano
    public function getPlanLimitsAttribute()
    {
        $plan = $this->getPlan();
        
        return [
            'max_workspaces' => $plan->max_workspaces,
            'max_topics' => $plan->max_topics,
            'max_fields' => $plan->max_fields,
            'can_export' => $plan->can_export,
            'can_use_api' => $plan->can_use_api,
            'current_workspaces' => $this->workspaces()->count(),
            'current_topics' => $this->topics()->count(),
            'remaining_workspaces' => $this->getRemainingWorkspacesCount(),
            'current_fields' => $this->getCurrentFieldsCount(),
            'remaining_fields' => $this->getRemainingFieldsCount()
        ];
    }

    public function getRemainingWorkspacesCount(): int
    {
        if ($this->isAdmin()) return PHP_INT_MAX;
        
        $plan = $this->getPlan();
        $currentWorkspaces = $this->workspaces()->count();
        
        return max(0, $plan->max_workspaces - $currentWorkspaces);
    }

    public function getRemainingFieldsCount($workspaceId = null, $topicId = null): int
    {
        if ($this->isAdmin() || $this->isPremium() || $this->isPro()) {
            return PHP_INT_MAX;
        }
        
        $plan = $this->getPlan();
        
        // Se for plano ilimitado, retornar um número grande
        if ($plan->max_fields === 0) {
            return PHP_INT_MAX;
        }
        
        $currentCount = $this->getCurrentFieldsCount($workspaceId, $topicId);
        return max(0, $plan->max_fields - $currentCount);
    }


    /**
     * Get user's preferred language
     */
    public function getPreferredLanguage(): string
    {
        return $this->language ?? config('app.locale');
    }

    /**
     * Set user's preferred language
     */
    public function setLanguage(string $language): bool
    {
        $availableLocales = array_keys(config('app.available_locales', ['pt_BR' => 'Português']));
        
        if (!in_array($language, $availableLocales)) {
            return false;
        }

        $this->update(['language' => $language]);
        return true;
    }

    /**
     * Verificar se usuário tem assinatura ativa
     */
    public function hasActiveSubscription(): bool
    {
        return in_array($this->status, [self::STATUS_ACTIVE, self::STATUS_TRIAL]) && 
               $this->plan_expires_at && 
               $this->plan_expires_at->isFuture();
    }

    /**
     * Verificar se está em trial
     */
    public function isOnTrial(): bool
    {
        return $this->status === self::STATUS_TRIAL && 
               $this->plan_expires_at && 
               $this->plan_expires_at->isFuture();
    }

    /**
     * Verificar se está em período de cortesia (cancelado mas ainda ativo)
     */
    public function isOnGracePeriod(): bool
    {
        return $this->status === self::STATUS_INACTIVE && 
               $this->plan_expires_at && 
               $this->plan_expires_at->isFuture();
    }

    /**
     * Verificar se assinatura está com pagamento pendente
     */
    public function hasPaymentIssues(): bool
    {
        return in_array($this->status, [self::STATUS_PAST_DUE, self::STATUS_UNPAID, self::STATUS_INCOMPLETE]);
    }

    /**
     * Determinar role baseado no status (sem current_plan_id)
     */
    private function getRoleFromStatus(): string
    {
        $roleName = $this->getRoleNames()->first();
        
        if ($roleName) {
            return $roleName;
        }

        // Mapear status para roles (sem current_plan_id)
        switch ($this->status) {
            case self::STATUS_ACTIVE:
            case self::STATUS_TRIAL:
                // Se tem status ativo mas não tem role, usar FREE
                return self::ROLE_FREE;
                        
            case self::STATUS_PAST_DUE:
            case self::STATUS_UNPAID:
            case self::STATUS_INCOMPLETE:
                // Usuários com problemas de pagamento mantêm role atual
                return $this->getRoleNames()->first() ?: self::ROLE_FREE;
                        
            case self::STATUS_INACTIVE:
            case self::STATUS_SUSPENDED:
            default:
                return self::ROLE_FREE;
        }
    }

    /**
     * Atualizar status do usuário (sem current_plan_id)
     */
    public function updateSubscriptionStatus(string $status, ?Plan $plan = null, ?\DateTime $expiresAt = null): void
    {
        $updates = ['status' => $status];
        
        if ($plan) {
            // Apenas atualizar a role
            if (in_array($status, [self::STATUS_ACTIVE, self::STATUS_TRIAL])) {
                $this->syncRoles([$plan->name]);
            }
        }
        
        if ($expiresAt) {
            $updates['plan_expires_at'] = $expiresAt;
        }
        
        $this->update($updates);
    }

    /**
     * Iniciar trial
     */
    public function startTrial(Plan $plan, int $trialDays = 14): void
    {
        $this->update([
            'status' => self::STATUS_TRIAL,
            'current_plan_id' => $plan->id,
            'plan_expires_at' => now()->addDays($trialDays)
        ]);
        
        $this->syncRoles([$plan->name]);
    }

    /**
     * Ativar assinatura
     */
    public function activateSubscription(Plan $plan, \DateTime $expiresAt): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'current_plan_id' => $plan->id,
            'plan_expires_at' => $expiresAt
        ]);
        
        $this->syncRoles([$plan->name]);
    }

    /**
     * Cancelar assinatura
     */
    public function cancelSubscription(bool $immediately = false): void
    {
        $freePlan = Plan::where('name', self::ROLE_FREE)->first();
        
        if ($immediately) {
            $this->update([
                'status' => self::STATUS_INACTIVE,
                'current_plan_id' => $freePlan->id,
                'plan_expires_at' => now(),
                'stripe_subscription_id' => null
            ]);
        } else {
            // Mantém features até o fim do período
            $this->update([
                'status' => self::STATUS_INACTIVE
                // plan_expires_at mantém a data original
            ]);
        }
        
        $this->syncRoles([self::ROLE_FREE]);
    }

    /**
     * Marcar como pendente de pagamento
     */
    public function markAsPastDue(): void
    {
        $this->update(['status' => self::STATUS_PAST_DUE]);
    }

    /**
     * Verifica se usuário tem assinatura ativa no Stripe (com debug)
     */
    public function hasActiveStripeSubscription(): bool
    {       
        if (!$this->stripe_id) {
            return false;
        }
        
        try {
            $isSubscribed = $this->subscribed('default');
            
            if ($isSubscribed) {
                $subscription = $this->getStripeSubscription();
            }
            
            return $isSubscribed;
            
        } catch (\Exception $e) {
            \Log::error('Erro em hasActiveStripeSubscription: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obter assinatura Stripe de forma robusta
     */
    public function getStripeSubscription()
    {        
        if (!$this->stripe_id) {
            return null;
        }
        
        try {
            $subscription = $this->subscription('default');
            
            if ($subscription) {
                \Log::info('✅ Subscription encontrada:', [
                    'id' => $subscription->stripe_id,
                    'status' => $subscription->stripe_status,
                    'price' => $subscription->stripe_price
                ]);
            } else {                
                // Tentar buscar manualmente
                $manualSubscription = $this->subscriptions()
                    ->whereIn('stripe_status', ['active', 'trialing'])
                    ->orderBy('created_at', 'desc')
                    ->first();
                    
                if ($manualSubscription) {
                    return $manualSubscription;
                }
            }
            
            return $subscription;
            
        } catch (\Exception $e) {
            \Log::error('Erro em getStripeSubscription: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obter plano atual baseado APENAS na role do usuário
     */
    public function getPlan()
    {
        // SEMPRE usar a role atual do sistema de permissions
        $roleName = $this->getRoleNames()->first();
        
        // Se não tem role, definir como FREE
        if (!$roleName) {
            $this->syncRoles([self::ROLE_FREE]);
            $roleName = self::ROLE_FREE;
        }
        
        // Buscar plano baseado na role
        $plan = Plan::where('name', $roleName)->first();
        
        // Fallback para FREE se plano não existir
        if (!$plan) {
            \Log::warning("Plano não encontrado para role: {$roleName}, usando FREE como fallback");
            $this->syncRoles([self::ROLE_FREE]);
            return Plan::where('name', self::ROLE_FREE)->first();
        }
        
        return $plan;
    }

    /**
     * Detectar plano baseado no Stripe Price ID
     */
    private function getPlanByStripePriceId($priceId)
    {
        $prices = config('services.stripe.prices');
        
        \Log::info('Buscando plano para price_id: ' . $priceId, [
            'prices_config' => $prices
        ]);
        
        foreach ($prices as $planName => $stripePriceId) {
            if ($stripePriceId === $priceId) {
                $plan = Plan::where('name', $planName)->first();
                \Log::info('Plano encontrado: ' . $planName, ['plan_id' => $plan ? $plan->id : 'null']);
                return $plan;
            }
        }
        
        // Fallback para Pro se não encontrar
        $fallbackPlan = Plan::where('name', self::ROLE_PRO)->first();
        \Log::warning('Plano não encontrado para price_id, usando fallback: ' . self::ROLE_PRO);
        return $fallbackPlan;
    }

    /**
     * Sincronizar status com assinatura Stripe (sem current_plan_id)
     */
    public function syncStripeSubscriptionStatus(): void
    {
        \Log::info('Sincronizando status do Stripe para usuário: ' . $this->email);

        if (!$this->hasActiveStripeSubscription()) {
            \Log::info('Usuário não tem assinatura ativa no Stripe - definindo como FREE');
            
            $this->update([
                'status' => self::STATUS_INACTIVE,
            ]);
            
            $this->syncRoles([self::ROLE_FREE]);
            \Log::info('Usuário definido como FREE');
            return;
        }

        $subscription = $this->getStripeSubscription();
        $priceId = $subscription->stripe_price;
        $plan = $this->getPlanByStripePriceId($priceId);
        
        if ($plan && $subscription->active()) {
            \Log::info('Atualizando usuário para plano: ' . $plan->name);
            
            $this->update([
                'status' => self::STATUS_ACTIVE,
                'plan_expires_at' => $subscription->ends_at
            ]);
            
            // APENAS SINCRONIZAR ROLE
            $this->syncRoles([$plan->name]);
            
            \Log::info('Role atualizada para: ' . $plan->name);

        } else {
            \Log::warning('Plano não encontrado ou assinatura inativa');
        }
    }

    /**
     * Histórico de atividades do usuário
     */
    public function activities()
    {
        return $this->hasMany(UserActivity::class);
    }

    /**
     * Bloquear/desbloquear conta
     */
    public function suspend(): void
    {
        $this->update(['status' => self::STATUS_SUSPENDED]);
    }

    public function activate(): void
    {
        $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Accessor para último login formatado
     */
    public function getLastLoginFormattedAttribute(): string
    {
        if (!$this->last_login_at) {
            return 'Nunca';
        }
        
        return $this->last_login_at->timezone($this->timezone ?? 'UTC')
            ->format('d/m/Y H:i');
    }

    /**
     * Verificar se usuário está online (últimos 5 minutos)
     */
    public function getIsOnlineAttribute(): bool
    {
        if (!$this->last_login_at) {
            return false;
        }
        
        return $this->last_login_at->diffInMinutes(now()) <= 5;
    }

    /**
     * Scope para usuários online
     */
    public function scopeOnline($query)
    {
        return $query->where('last_login_at', '>=', now()->subMinutes(5));
    }

    /**
     * Scope para usuários com atividade recente
     */
    public function scopeRecentlyActive($query, $minutes = 60)
    {
        return $query->where('last_login_at', '>=', now()->subMinutes($minutes));
    }
}
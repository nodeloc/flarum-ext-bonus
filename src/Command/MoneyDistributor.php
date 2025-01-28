<?php

namespace Nodeloc\Bonus\Command;

use Carbon\Carbon;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Mattoid\MoneyHistory\Event\MoneyHistoryEvent;
use Nodeloc\Bonus\BonusListItem;
use Nodeloc\Bonus\Notification\PostBonusBlueprint;
use Flarum\Notification\NotificationSyncer;
use Mattoid\MoneyHistory\model\UserMoneyHistory;
use Symfony\Contracts\Translation\TranslatorInterface;


class MoneyDistributor extends Command
{
    protected $notifications;

    protected $signature = 'bonus:postBonus';
    protected $description = 'Post bonus to group.';
    protected $events;
    protected $prefix = 'Bonus #';
    /**
     * @var SettingsRepositoryInterface
     */
    private $settings;

    /**
     * @var NotificationSyncer
     */
    protected $translator;
    public function __construct(SettingsRepositoryInterface $settings, Dispatcher $events,  NotificationSyncer $notifications, TranslatorInterface $translator)
    {
        parent::__construct();
        $this->settings = $settings;
        $this->notifications = $notifications;
        $this->events = $events;
        $this->translator = $translator;
        $this->notifications = $notifications;
    }

    public function handle()
    {
        $this->info('Bonus Start.');

        $currentTime = Carbon::now();

        // 查询所有的 bonus_list 记录
        $bonuses = BonusListItem::all();

        foreach ($bonuses as $bonus) {
            $groupId = $bonus->group_id;
            $amount = $bonus->amount;
            $reason = $bonus->content;
            $scheduleType = $bonus->schedule_type;
            $scheduleTime = $bonus->schedule_time;

            if ($scheduleType == 0 && !$bonus->is_post) {
                $this->info('Bonus title:'.$reason.',Amount:'.$amount.'Schedule time:'.$scheduleTime);
                // 一次性分发逻辑
                if (Carbon::parse($scheduleTime)->lessThanOrEqualTo($currentTime)) {
                    $this->distributeBonusOnce($groupId, $amount, $reason);
                    $bonus->is_post = 1; // 标记为已发放
                    $bonus->save();
                }
            } elseif ($scheduleType == 1) {
                // 周期性分发逻辑：检查是否已经发放本月奖金
                $lastPostTime = $bonus->last_post_time ? Carbon::parse($bonus->last_post_time) : null;
                $isSameMonth = $lastPostTime && $lastPostTime->isSameMonth($currentTime);

                if (!$isSameMonth && $currentTime->day == 1 && $currentTime->hour == 0) {
                    $this->distributeBonusRecurring($groupId, $amount, $reason);
                    $bonus->last_post_time = $currentTime;
                    $bonus->save();
                }
            }
        }
        $this->info('Done.');

    }

    /**
     * 一次性分发奖金到指定用户组
     *
     * @param int $groupId
     * @param float $amount
     * @param string $reason
     */
    private function distributeBonusOnce(int $groupId, float $amount, string $reason)
    {
        $this->info('distributeBonusOnce');
        $users = User::whereHas('groups', function ($query) use ($groupId) {
            $query->where('id', $groupId);
        })->get();
        foreach ($users as $user) {
            $user->increment('money', $amount);
            $this->sendNotification($user, $amount, $reason);
            $this->recordMoneyHistory($user, $amount, $reason);
        }
    }

    /**
     * 周期性分发奖金到指定用户组
     *
     * @param int $groupId
     * @param float $amount
     * @param string $reason
     */
    private function distributeBonusRecurring(int $groupId, float $amount, string $reason)
    {
        $this->info('distributeBonusRecurring');
        $users = User::whereHas('groups', function ($query) use ($groupId) {
            $query->where('id', $groupId);
        })->get();
        foreach ($users as $user) {
            $user->increment('money', $amount);
            $this->sendNotification($user, $amount, $reason);
            $this->recordMoneyHistory($user, $amount, $reason);
        }
    }

    /**
     * 发送通知
     *
     * @param User $user
     * @param float $amount
     * @param string $reason
     */
    private function sendNotification(User $user, float $amount, string $reason)
    {
        $this->notifications->sync(
            new PostBonusBlueprint($amount, $reason), // 自定义通知蓝图
            [$user]
        );
    }

    /**
     * 记录 Money History
     *
     * @param User $user
     * @param float $amount
     * @param string $reason
     */
    private function recordMoneyHistory(User $user, float $amount, string $reason)
    {
        $userMoneyHistory = new UserMoneyHistory();
        $userMoneyHistory->user_id = $user->id;
        $userMoneyHistory->type = $amount > 0 ? "C" : "D";
        $userMoneyHistory->money = abs($amount);
        $userMoneyHistory->source = 'Bonus';
        $userMoneyHistory->source_desc = $reason;
        $userMoneyHistory->balance_money = $user->money - $amount;
        $userMoneyHistory->last_money = $user->money;
        $userMoneyHistory->create_user_id = $user->id;
        $userMoneyHistory->change_time = Carbon::now();
        $userMoneyHistory->save();
    }

    public function info($string, $verbosity = null): void
    {
        parent::info($this->prefix . ' | ' . $string, $verbosity);
    }

}

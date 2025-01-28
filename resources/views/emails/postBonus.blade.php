{!! $translator->trans('nodeloc-bonus.email.body.postBonus', [
    '{recipient_display_name}' => $user->display_name,
    '{actor_display_name}' => $blueprint->actor->display_name,
    '{bonus_amount}' => $blueprint->amount,
    '{bonus_title}' => $blueprint->reason,
 ]) !!}

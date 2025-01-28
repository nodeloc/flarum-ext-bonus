<?php
namespace Nodeloc\Bonus\Notification;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\Notification\MailableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


class PostBonusBlueprint implements BlueprintInterface, MailableInterface
{
    public $amount;
    public $reason;
    public $actor;

    public function __construct($amount,$reason)
    {
        $this->amount = $amount;
        $this->reason = $reason;
    }

    /**
     * Get the model that is the subject of this activity.
     */
    public function getSubject()
    {
        return $this->reason;
    }

    /**
     * Get the data to be stored in the notification.
     */
    public function getData()
    {
        return [
            'amount' => $this->amount,
            'reason' => $this->reason,
        ];
    }

    /**
     * Get the serialized type of this activity.
     *
     * @return string
     */
    public static function getType()
    {
        return 'postBonus';
    }

    /**
     * Get the name of the view to construct a notification email with.
     *
     * @return array{text?: string, html?: string}
     */
    public function getEmailView()
    {
        return ['text' => 'nodeloc-bonus::emails.postBonus'];
    }

    /**
     * Get the subject line for a notification email.
     *
     * @return string
     */
    public function getEmailSubject(TranslatorInterface $translator)
    {
        return $translator->trans('nodeloc-bonus.email.subject.postBonus', [
            '{bonus_amount}' => $this->amount,
            '{bonus_title}' => $this->reason,
        ]);
    }

    public function getFromUser()
    {
        return null;
    }

    public static function getSubjectModel()
    {
        return null;
    }
}

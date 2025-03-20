<?php

namespace App\Notifications;

use App\Models\Tenants\Receivable;
use App\Models\Tenants\User;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class ReceivableDueDate extends Notification
{
    use Queueable;

    /**
     * @param Collection<Receivable> $receivables
     * @return void
     */
    public function __construct(private Collection $receivables) {}

    public function via(User $user)
    {
        if ($user->fcm_token) {
            if (env('FIREBASE_CREDENTIALS')) {
                return [FcmChannel::class, 'database'];
            }
        }

        return ['database'];
    }

    public function toArray(User $notifiable): array
    {
        $convertedArray = [];
        // array:5 [
        //     "id" => 28
        //     "name" => "Stock for Printer out of stock"
        //     "stock" => "The rest stock is 0"
        //     "hero_image" => "https://cdn4.iconfinder.com/data/icons/picture-sharing-sites/32/No_Image-1024.png"
        //     "route" => "/menu/product/stock"
        // ]

        return [

        ];
    }

    // public function toFcm(User $notifiable): FcmMessage
    // {
    //     $title = __('Receivable Due Date');
    //     $body = __('The receivable '.$this->receivables->name.' is due on '.$this->receivables->due_date->format('d/m/Y').'.');
    //
    //     return (new FcmMessage(
    //         notification: new FcmNotification(
    //             title: $title,
    //             body: $body
    //         )
    //     ))->data([
    //         'title' => $title,
    //         'body' => $body,
    //     ])
    //         ->custom([
    //             'android' => [
    //                 'notification' => [
    //                     'color' => '#0A0A0A',
    //                 ],
    //                 'fcm_options' => [
    //                     'analytics_label' => 'analytics',
    //                 ],
    //             ],
    //             'apns' => [
    //                 'fcm_options' => [
    //                     'analytics_label' => 'analytics',
    //                 ],
    //             ],
    //         ]);
    // }
}

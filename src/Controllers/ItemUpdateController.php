<?php

namespace Nodeloc\Bonus\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Formatter\Formatter;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Nodeloc\Bonus\BonusListItem;
use Nodeloc\Bonus\Serializers\BonusListItemSerializer;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ItemUpdateController extends AbstractShowController
{
    public $serializer = BonusListItemSerializer::class;

    public $include = [
        'group',
    ];

    protected function data(ServerRequestInterface $request, Document $document)
    {
        RequestUtil::getActor($request)->assertAdmin();

        $id = Arr::get($request->getQueryParams(), 'id');

        $attributes = Arr::get($request->getParsedBody(), 'data.attributes', []);

        /**
         * @var $item BonusListItem
         */
        $item = BonusListItem::query()->findOrFail($id);
        if (Arr::exists($attributes, 'amount')) {
            $item->amount = Arr::get($attributes, 'amount');
        }
        if (Arr::exists($attributes, 'content')) {
             $item->content = Arr::get($attributes, 'content');
        }
        if (Arr::exists($attributes, 'schedule_type')) {
            $item->schedule_type = Arr::get($attributes, 'schedule_type');
        }
        if (Arr::exists($attributes, 'schedule_time')) {
            $item->schedule_time = Arr::get($attributes, 'schedule_time');
        }

        $item->save();

        return $item;
    }
}

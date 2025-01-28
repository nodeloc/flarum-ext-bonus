<?php

namespace Nodeloc\Bonus\Controllers;

use Flarum\Api\Controller\AbstractDeleteController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Nodeloc\Bonus\BonusListItem;
use Psr\Http\Message\ServerRequestInterface;

class ItemDeleteController extends AbstractDeleteController
{
    protected function delete(ServerRequestInterface $request)
    {
        RequestUtil::getActor($request)->assertAdmin();

        $id = Arr::get($request->getQueryParams(), 'id');

        $item = BonusListItem::query()->findOrFail($id);

        $item->delete();
    }
}

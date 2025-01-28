import Model from 'flarum/common/Model';

export default class BonusListItem extends Model {
  content = Model.attribute('content');
  amount = Model.attribute('amount');
  schedule_type = Model.attribute('schedule_type');
  schedule_time = Model.attribute('schedule_time');
  group = Model.hasOne('group');
}

import app from 'flarum/admin/app';
import MoneyScheduleSettingsPage from './components/MoneyScheduleSettingsPage';
import BonusListItem from "../common/models/BonusListItem";

app.initializers.add('nodeloc/flarum-ext-bonus', () => {
  app.store.models['bonus-list-items'] = BonusListItem;

  app.extensionData
    .for('nodeloc-bonus')
    .registerPage(MoneyScheduleSettingsPage);
});

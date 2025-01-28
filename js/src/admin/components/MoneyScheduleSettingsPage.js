import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import BonusListConfigurator from './BonusListConfigurator'
export default class MoneyScheduleSettingsPage extends ExtensionPage {
  content() {
    return m('.ExtensionPage-settings', m('.container', [
      m('.Form-group', m(BonusListConfigurator)),
    ]));
  }
}

import Button from 'flarum/common/components/Button';
import Dropdown from 'flarum/common/components/Dropdown';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import Group from 'flarum/common/models/Group';
import icon from 'flarum/common/helpers/icon';

/* global app, m */

const translationPrefix = 'nodeloc-bonus.admin.items.';

export default class SelectFieldOptionEditor {
  oninit() {
    this.items = null;

    app.request({
      method: 'GET',
      url: app.forum.attribute('apiUrl') + '/bonus-list',
    }).then(response => {
      this.items = app.store.pushPayload(response);
      m.redraw();
    });
  }

  view() {
    const existingGroups = this.items === null ? [] : this.items.map(item => item.group().id());
    const scheduleTypes = {
      '0': '一次性',
      '1': '每月',
    };
    return m('table.BonusListTable', m('tbody', [
      this.items === null ? m('tr', m('td', LoadingIndicator.component())) : this.items.map((item, index) => m('tr', [
        m('td', item.group().namePlural()),
        m('td', m('input.FormControl', {
          type: 'number',
          min: 0,
          step: 'any',
          onchange: event => {
            item.save({
              amount: event.target.value,
            }).then(() => {
              m.redraw();
            });
          },
          value: item.amount(),
        })),
        m('td', m('input.FormControl', {
          onchange: event => {
            item.save({
              content: event.target.value,
            }).then(() => {
              m.redraw();
            });
          },
          value: item.content(),
        })),
        m('td', m('select.FormControl', {
          onchange: event => {
            item.save({
              schedule_type: event.target.value,
            }).then(() => {
              m.redraw();
            });
          },
          value: item.attribute('schedule_type') || '0',
        }, Object.entries(scheduleTypes).map(([value, label]) =>
          m('option', { value }, label)
        ))),
        m('td', m('input.FormControl', {
          type: 'datetime-local',
          onchange: event => {
            item.save({
              schedule_time: event.target.value,
            }).then(() => {
              m.redraw();
            });
          },
          value: item.attribute('schedule_time') || new Date().toISOString().slice(0, 16),
        })),
        m('td', m('button.Button.Button--danger', {
          onclick: event => {
            event.preventDefault(); // Do not close the settings modal
            item.delete().then(() => {
              this.items.splice(index, 1);
              m.redraw();
            });
          },
        }, icon('fas fa-times'))),
      ])),
      m('tr', m('td', {
        colspan: 5,
      }, Dropdown.component({
        label: app.translator.trans(translationPrefix + 'add'),
        buttonClassName: 'Button',
      }, app.store.all('groups')
        .filter(group => {
          if (group.id() === Group.MEMBER_ID || group.id() === Group.GUEST_ID) {
            // Do not suggest "virtual" groups
            return false;
          }

          // Do not suggest groups already in use
          return existingGroups.indexOf(group.id()) === -1;
        })
        .map(group => Button.component({
          onclick: () => {
            app.request({
              method: 'POST',
              url: app.forum.attribute('apiUrl') + '/bonus-list-items',
              body: {
                data: {
                  attributes: {
                    groupId: group.id()
                  },
                },
              },
            }).then(response => {
              this.items.push(app.store.pushPayload(response));
              m.redraw();
            });
          },
        }, group.namePlural()))))),
    ]));
  }
}

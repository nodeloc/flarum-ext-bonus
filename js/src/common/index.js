import app from 'flarum/common/app';

app.initializers.add('nodeloc/flarum-ext-bonus', () => {
  console.log('[nodeloc/flarum-ext-bonus] Hello, forum and admin!');
});

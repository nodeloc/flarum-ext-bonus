import app from 'flarum/forum/app';

app.initializers.add('nodeloc/flarum-ext-bonus', () => {
  console.log('[nodeloc/flarum-ext-bonus] Hello, forum!');
});

import { createApp } from 'vue';

import App from '../../vue/Base.vue';
import store from '../../store/store';
import router from './router';
import flowtogether from '@flowtogether';
import flowtogetherMixin from '@flowtogether-vue/FlowTogetherMixin';
import BaseMixin from './BaseMixin';

let app = createApp(App);

app.use(router);
app.use(store);
app.use(flowtogether);
app.mixin(flowtogetherMixin);
app.mixin(BaseMixin);

app.mount('#app');

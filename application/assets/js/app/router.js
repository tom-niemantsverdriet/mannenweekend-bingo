// Routes

import Bingo from '../../vue/bingo/Bingo.vue';
import Leaderboard from '../../vue/leaderboard/Leaderboard.vue';
import LeaderboardUser from '../../vue/leaderboard/LeaderboardUser.vue';

let routes = [
    {path: '/', component: Bingo, name: 'app.bingo'},
    {path: '/leaderboard', component: Leaderboard, name: 'app.leaderboard'},
    {path: '/leaderboard/:id', component: LeaderboardUser, name: 'app.leaderboard.user'},
];

// Create router

import {createRouter} from '@flowtogether-vue/CreateRouter';
export default createRouter(routes);

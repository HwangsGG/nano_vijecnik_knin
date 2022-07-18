function page (path) {
  return () => import(/* webpackChunkName: '' */ `~/pages/${path}`).then(m => m.default || m)
}

export default [
  { path: '/', name: 'welcome', component: page('welcome.vue') },

  { path: '/login', name: 'login', component: page('auth/login.vue') },
  { path: '/register', name: 'register', component: page('auth/register.vue') },
  { path: '/password/reset', name: 'password.request', component: page('auth/password/email.vue') },
  { path: '/password/reset/:token', name: 'password.reset', component: page('auth/password/reset.vue') },
  { path: '/email/verify/:id', name: 'verification.verify', component: page('auth/verification/verify.vue') },
  { path: '/email/resend', name: 'verification.resend', component: page('auth/verification/resend.vue') },

  { path: '/home', name: 'home', component: page('home.vue') },
  {
    path: '/settings', component: page('settings/index.vue'),
    children: [
      { path: '', redirect: { name: 'settings.profile' } },
      { path: 'profile', name: 'settings.profile', component: page('settings/profile.vue') },
      { path: 'password', name: 'settings.password', component: page('settings/password.vue') }
    ]
  },

  ///city
  { path: '/cities', name:"cities.all", component: page('cities/Cities.vue')},
  { path: '/city/:id', name: 'showCity', component:page('cities/Show_city.vue')},

  ///roles
  { path: '/roles', name:"roles.all", component: page('roles/Roles.vue')},
  { path: '/role/:id', name: 'showRole', component:page('roles/ShowRole.vue')},

  ///permissions
  { path: '/permissions', name:"permissions.all", component: page('permissions/Permissions.vue')},
  { path: '/permission/:id', name: 'showPermission', component:page('permissions/ShowPermission.vue')},

  ///users
  { path: '/users', name:"users.all", component: page('users/Users.vue')},
  { path: '/user/:id', name: 'showUser', component:page('users/ShowUser.vue')},

  //markers
  { path: '/markers', name:"markers.all", component: page('markers/Markers.vue')},

  //groups
  { path: '/groups', name:"groups.all", component: page('groups/Groups.vue')},
  { path: '/group/:id', name: 'showGroup', component:page('groups/ShowGroup.vue')},

  { path: '*', component: page('errors/404.vue') }
]

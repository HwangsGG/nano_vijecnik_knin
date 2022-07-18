import store from '~/store'
import Cookies from "js-cookie";

/**
 * This is middleware to check the current user role.
 *
 * middleware: 'role:admin,manager',
 */

export default (to, from, next, roles) => {
  //provjeri najprije da li je korisnik prijavljen, ukoliko nije vrati ga na login
  if (!store.getters['auth/check']) {
    Cookies.set('intended_url', to.path)
    next({ name: 'login' })
  }else{

  // Grab the user
  const user = store.getters['auth/user']

  //Variable for currently roles
  const  rolesForUser=[];

  for (let i=0;i<user.roles.length;i++){
    rolesForUser.push(store.getters['auth/user'].roles[i].name)
  }

  // Split roles into an array
  roles = roles.split(',')

  // Check if the user has one of the required roles...
  if (!roles.some( ai => rolesForUser.includes(ai) )) {
    next('/unauthorized')
  }

  next()
  }
}

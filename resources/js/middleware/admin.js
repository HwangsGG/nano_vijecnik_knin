import store from '~/store'


export default (to, from, next) => {

  const  roles=[];
  console.log(store.getters['auth/hasRole']);
  for (let i=0;i<store.getters['auth/user'].roles.length;i++){
    roles.push(store.getters['auth/user'].roles[i].name)
  }
  if (!roles.includes("Admin")) {
    next({ name: 'home' })
  } else {
    next()
  }
}

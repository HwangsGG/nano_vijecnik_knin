import store from '~/store'

export default (to, from, next) => {
  /***
   *
   * @type {*[]}
   * varijabla u kojoj se spremaju ovlasti korisnika
   */
  const  roles=[];

  /***
   * prodi kroz for petlju i sve role korisnika stavi u varijablu roles
   */
  for (let i=0;i<store.getters['auth/user'].roles.length;i++){
    roles.push(store.getters['auth/user'].roles[i].name)
  }

  /***
   * provjeri da li se rola nalazi u varijabli, ukoliko da onbda dopusti pristup inače ne
   */
  if (!roles.includes("Super admin")) {
    next({ name: 'home' })
  } else {
    next()
  }
}

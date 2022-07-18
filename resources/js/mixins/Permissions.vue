<script>
export default {
  methods: {
    /*Metoda koja provjerava ovlasti koje prijavlkjeni korisnik ima*/
   /* $can(permissionName) {
      return Permissions.indexOf(permissionName) !== -1;
    },*/
    /**
     *
     * @param array
     * @param item
     * @returns {boolean}
     * Metoda iz arraya provjerava stavku, ako je item u array-u vraca true
     * Metoda kreirana rucno
     */

    $can(array,item){
      /*check direct permissions*/
      for (let i=0;i<array.permissions.length;i++){
          if (array.permissions[i].name===item){
            return true;
        }
      }
      /*check permissions with role*/
      for (let i=0;i<array.permission.length;i++){
        if (array.permission[i].name===item){
          return true;
        }
      }

      return false;
    }
  },
};
</script>

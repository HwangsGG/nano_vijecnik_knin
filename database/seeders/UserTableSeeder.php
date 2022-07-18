<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            ['Admin Admincic','admin@sibenik.hr',bcrypt('sibenik'),2,1,1,'{\”club\”: \”Admin\”}',1],
/*            ['Predsjenik Skupštincic','predsjednik@sibenik.hr',bcrypt('sibenik'),2,1,1,'{\”club\”: \”Admin\”}'],
            ['Zamjenik Predsjednikovic','zamjenik@sibenik.hr',bcrypt('sibenik'),2,1,1,'{\”club\”: \”Admin\”}'],
            ['Predsjenik Klubcic','pred.klub@sibenik.hr',bcrypt('sibenik'),2,1,1,'{\”club\”: \”Admin\”}'],
            ['Zamjenik Klubcic','zamjenik.klub@sibenik.hr',bcrypt('sibenik'),2,1,1,'{\”club\”: \”Admin\”}'],
            ['Vijecnik Vijecic','vijecnik@sibenik.hr',bcrypt('sibenik'),2,1,1,'{\”club\”: \”Admin\”}'],
            ['Web Korisnikovic','web@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”Admin\”}'],*/
            ['Nediljko Dujić','nediljko.dujic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”HDZ\”}',2],
            ['Ante Rakić','ante.rakic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”HDZ\”}',3],
            ['Tomislav Lucić','tomiclav.lucic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”HDZ\”}',4],
            ['Robert Marić','robert.maric@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”HDZ\”}',5],
            ['Niveska Vlaić','niveska.vlkaic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”HDZ\”}',6],
            ['Gordan Tabula','gordan.tabula@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”HDZ\”}',6],
            ['Ante Vrcić','ante.vrcic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”HDZ\”}',6],
            ['Anita Aužina','anita.auzina@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”HDZ\”}',6],
            ['Tanja Radić Lakoš','tanja.radic.lakos@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”HDZ\”}',6],
            ['Marijo Baić','marijo.baic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”HDZ\”}',6],
            ['Marko Parat','marko.parat@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”HDZ\”}',6],
            ['Ivica Bratić','ivica.bratic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”HDZ\”}',6],
            ['Katarina Požar','katarina.pozar@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”HDZ\”}',6],
            ['Joso Smolić','joso.smolic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”HDZ\”}',6],
            ['Stipe Petrina','stipe.petrina@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”NEZAVISNA LISTA STIPE PETRINA - NLSP\”}',4],
            ['Goran Mladenković','goran.mladenkovic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”NEZAVISNA LISTA STIPE PETRINA - NLSP\”}',5],
            ['Ivan Melvan','ivan.melvan@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”NEZAVISNA LISTA STIPE PETRINA - NLSP\”}',6],
            ['Sanja Radin Mačukat','sanja.radin.macukat@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”NEZAVISNA LISTA STIPE PETRINA - NLSP\”}',6],
            ['Dijana Kulaš','dijana.kulas@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”NEZAVISNA LISTA STIPE PETRINA - NLSP\”}',6],
            ['Ljubo Županović','ljubo.zupanovic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”NEZAVISNA LISTA STIPE PETRINA - NLSP\”}',6],
            ['Ivana Šimat','ivana.simat@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”NEZAVISNA LISTA STIPE PETRINA - NLSP\”}',6],
            ['Ivan Gulam','ivan.gulam@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”nezavisni vijećnik\”}',4],
            ['Marko Grubelić','marko.grubelic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”nezavisni vijećnik\”}',5],
            ['Toni Turčinov','toni.turcinov@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”nezavisni vijećnikDZ\”}',6],
            ['Lidija Slavić','lidija.slavic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”nezavisni vijećnik\”}',6],
            ['Nikša Kulušuić','niksa.kulusuic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”nezavisni vijećnik\”}',6],
            ['Goran Šimić','goran.simic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”nezavisni vijećnik\”}',6],
            ['Joško Šupe','josko.supe@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”SDP\”}',4],
            ['Karlo Klarin','karlo.klarin@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”SDP\”}',5],
            ['Ivana Vučenović','ivana.vucenovic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”SDP\”}',6],
            ['Marina Paškalin','marina.paskalin@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”MOST\”}',4],
            ['Višnja Gojanović','visnja.gojanovic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”DOMOVINSKI POKRET\”}',4],
            ['Branko Dželalija','branko.dzelalija@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”MOST\”}',5],
            ['Sanja Kosijer','sanja.kosijer@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”SDSS\”}',4],
            ['Borislav Šarić','borislav.saric@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”SDSS\”}',5],
            ['Svemirka Lalić Krapp','svemirka.lalic.krapp@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”SDSS\”}',6],
            ['Zdravko Šegan','zdravko.segan@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”SDSS\”}',6],
            ['Florijan Žižić','florijan.zizic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”nezavisni vijećnik\”}',6],
            ['Adrijana Franjkić','adrijana.franjkic@sibenik.hr',bcrypt('sibenik'),2,2,1,'{\”club\”: \”nezavisni vijećnik\”}',6],


        ];

        foreach ($users as $key => $user) {
            $new_user = User::create([
                            'name' => $user[0],
                            'email'=> $user[1],
                            'password'=> $user[2],
                            'account_id'=> $user[3],
                            'city_id'=> $user[4],
                            'active'=> $user[5],
                            'email_verified_at'=>now(),
                            'data' => $user[6]
                        ]);

            $new_user->assignRole([
                $user[7]
            ]);
        }
    }
}

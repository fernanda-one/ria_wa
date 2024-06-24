<?php

namespace Database\Seeders;

use App\Models\MstArea;
use App\Models\MstCity;
use App\Models\MstRegion;
use App\Models\MstShop;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regions = [
            'BANTEN',
            'JAKARTA & BEKASI',
            'JAWA BARAT 1',
            'JAWA BARAT 2',
            'JAWA BARAT 3',
            'JAWA BARAT 4',
            'JAWA BARAT 5',
            'JAWA BARAT 6',
            'JAWA TENGAH',
            'JAWA TIMUR',
            'SUMATERA UTARA'
        ];

        $areas = [
            'JAWA TIMUR &BALI',
            'BANDUNG 1',
            'BANDUNG 2',
            'BANTEN',
            'BEKASI',
            'BOGOR 1',
            'BOGOR 2',
            'CIANJUR',
            'CIBINONG',
            'CIKAMPEK',
            'D.I.Y',
            'GARUT',
            'JAKARTA',
            'JAWA TENGAH ',
            'KAB. BANDUNG',
            'KAB. SUKABUMI',
            'MARATASU',
            'MEDAN 1',
            'MEDAN 2',
            'PANTURA',
            'SUBANG',
            'SUKABUMI KOTA',
            'TANGERANG',
            'TASIKMALAYA'
        ];

        $citys = [
            'AKSARA BALARAJA',
            'BCP',
            'BEKASI',
            'BINJAI',
            'BOGOR PLAZA',
            'BTM',
            'CIAMIS',
            'CIANJUR',
            'CIBADAK',
            'CIBINONG',
            'CICURUG',
            'CIKARANG',
            'CIKUPA',
            'CILEDUG',
            'CILEGON',
            'CILEUNGSI',
            'CIMAHI',
            'CIPANAS',
            'CIPUTAT',
            'CIREBON',
            'DALEM KAUM',
            'DAYEUH KOLOT',
            'DEPOK',
            'DEWI SARTIKA',
            'GARUT',
            'INDRAMAYU',
            'JAKARTA',
            'JATIBARANG',
            'KABANJAHE',
            'KADIPATEN',
            'KARAWANG',
            'KEDIRI',
            'KEPATIHAN',
            'KRANJI',
            'KUNINGAN',
            'KUTA',
            'LABUAN',
            'LB. PAKAM',
            'LEMBANG',
            'MADIUN',
            'MAGELANG',
            'MAJALAYA',
            'MALANG',
            'MEDAN',
            'PAMANUKAN',
            'PARUNG',
            'PASAR BARU',
            'PEKALONGAN',
            'PEMALANG',
            'PERMAI',
            'PESAWARAN',
            'PETISAH',
            'PG',
            'PLARA',
            'PONDOK GEDE',
            'PRINGGAN',
            'PSR. ANYAR',
            'PURWAKARTA',
            'RANCAEKEK',
            'RANGKASBITUNG',
            'SADANG',
            'SERANG',
            'SLAWI (TEGAL)',
            'SOREANG ',
            'SUBANG',
            'SUKABUMI',
            'SUMEDANG ',
            'TANGERANG',
            'TASIKMALAYA',
            'TEGAL',
            'TELUK NAGA',
            'YOGYAKARTA',
        ];

        $shops = [
            ['name' => 'VB 14', 'code' => '2014'],
            ['name' => 'VB 12C', 'code' => '2013'],
            ['name' => 'VB 12', 'code' => '2012'],
            ['name' => 'VB 11', 'code' => '2011'],
            ['name' => 'VB 10', 'code' => '2010'],
            ['name' => 'VB 09', 'code' => '2009'],
            ['name' => 'VB 08', 'code' => '2008'],
            ['name' => 'VB 05', 'code' => '2005'],
            ['name' => 'VB 04', 'code' => '2004'],
            ['name' => 'VB 03', 'code' => '2003'],
            ['name' => 'VB 02', 'code' => '2002'],
            ['name' => 'VB 01', 'code' => '2001'],
            ['name' => 'RB 45A', 'code' => '1245'],
            ['name' => 'RB 22A', 'code' => '1222'],
            ['name' => 'RB 21A', 'code' => '1221'],
            ['name' => 'RB 07A', 'code' => '1207'],
            ['name' => 'RB 03A', 'code' => '1203'],
            ['name' => 'RB 86', 'code' => '1086'],
            ['name' => 'RB 85', 'code' => '1085'],
            ['name' => 'RB 84', 'code' => '1084'],
            ['name' => 'RB 83', 'code' => '1083'],
            ['name' => 'RB 82', 'code' => '1082'],
            ['name' => 'RB 81', 'code' => '1081'],
            ['name' => 'RB 80', 'code' => '1080'],
            ['name' => 'RB 79', 'code' => '1079'],
            ['name' => 'RB 77', 'code' => '1077'],
            ['name' => 'RB 76', 'code' => '1076'],
            ['name' => 'RB 75', 'code' => '1075'],
            ['name' => 'RB 74', 'code' => '1074'],
            ['name' => 'RB 73', 'code' => '1073'],
            ['name' => 'RB 72', 'code' => '1072'],
            ['name' => 'RB 71', 'code' => '1071'],
            ['name' => 'RB 70', 'code' => '1070'],
            ['name' => 'RB 69', 'code' => '1069'],
            ['name' => 'RB 68', 'code' => '1068'],
            ['name' => 'RB 67', 'code' => '1067'],
            ['name' => 'RB 66', 'code' => '1066'],
            ['name' => 'RB 65', 'code' => '1065'],
            ['name' => 'RB 64', 'code' => '1064'],
            ['name' => 'RB 63', 'code' => '1063'],
            ['name' => 'RB 62', 'code' => '1062'],
            ['name' => 'RB 61', 'code' => '1061'],
            ['name' => 'RB 60', 'code' => '1060'],
            ['name' => 'RB 59', 'code' => '1059'],
            ['name' => 'RB 58', 'code' => '1058'],
            ['name' => 'RB 57', 'code' => '1057'],
            ['name' => 'RB 56', 'code' => '1056'],
            ['name' => 'RB 54', 'code' => '1054'],
            ['name' => 'RB 52', 'code' => '1052'],
            ['name' => 'RB 51', 'code' => '1051'],
            ['name' => 'RB 50', 'code' => '1050'],
            ['name' => 'RB 49', 'code' => '1049'],
            ['name' => 'RB 48', 'code' => '1048'],
            ['name' => 'RB 47', 'code' => '1047'],
            ['name' => 'RB 46', 'code' => '1046'],
            ['name' => 'RB 44', 'code' => '1044'],
            ['name' => 'RB 43', 'code' => '1043'],
            ['name' => 'RB 42', 'code' => '1042'],
            ['name' => 'RB 41', 'code' => '1041'],
            ['name' => 'RB 40', 'code' => '1040'],
            ['name' => 'RB 39', 'code' => '1039'],
            ['name' => 'RB 38', 'code' => '1038'],
            ['name' => 'RB 37', 'code' => '1037'],
            ['name' => 'RB 36', 'code' => '1036'],
            ['name' => 'RB 35', 'code' => '1035'],
            ['name' => 'RB 34', 'code' => '1034'],
            ['name' => 'RB 32', 'code' => '1032'],
            ['name' => 'RB 31', 'code' => '1031'],
            ['name' => 'RB 30', 'code' => '1030'],
            ['name' => 'RB 29', 'code' => '1029'],
            ['name' => 'RB 28', 'code' => '1028'],
            ['name' => 'RB 27', 'code' => '1027'],
            ['name' => 'RB 26', 'code' => '1026'],
            ['name' => 'RB 25', 'code' => '1025'],
            ['name' => 'RB 23', 'code' => '1023'],
            ['name' => 'RB 22', 'code' => '1022'],
            ['name' => 'RB 20', 'code' => '1020'],
            ['name' => 'RB 19', 'code' => '1019'],
            ['name' => 'RB 18', 'code' => '1018'],
            ['name' => 'RB 16', 'code' => '1016'],
            ['name' => 'RB 15', 'code' => '1015'],
            ['name' => 'RB 14', 'code' => '1014'],
            ['name' => 'RB J', 'code' => '1013'],
            ['name' => 'RB 12', 'code' => '1012'],
            ['name' => 'RB 11', 'code' => '1011'],
            ['name' => 'RB 10', 'code' => '1010'],
            ['name' => 'RB 09', 'code' => '1009'],
            ['name' => 'RB 08', 'code' => '1008'],
            ['name' => 'RB 06', 'code' => '1006'],
        ];

        foreach ($regions as $region) {
            MstRegion::factory()->create(['name' => $region]);
        }

        foreach ($areas as $area) {
            MstArea::factory()->create(['name' => $area]);
        }

        foreach ($shops as $shop) {
            MstShop::factory()->create($shop);
        }

        foreach ($citys as $city) {
            MstCity::factory()->create(['name' => $city]);
        }
    }
}

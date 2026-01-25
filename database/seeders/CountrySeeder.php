<?php
namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Mosab\Translation\Models\Translation;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Translation::where('translatable_type',Country::class)->delete();
        Country::truncate();
        Schema::enableForeignKeyConstraints();


        Country::create([
            'country_code' => 'AD',
            'phone_code' => '376',
            'name' => ['en' => 'Andorra' ,'ar' => 'أندورا'],
            'nationality' => ['en' => 'Andorran' , 'ar' =>  'أندوران'],
        ]);

        Country::create([
            'country_code' => 'AE',
            'phone_code' => '971',
            'name' => ['en' => 'United Arab Emirates' ,'ar' => 'الإمارات العربية المتحدة'],
            'nationality' => ['en' => 'Emirati' , 'ar' =>  'إماراتي'],
        ]);


        Country::create([
            'country_code' => 'AF',
            'phone_code' => '93',
            'name' => ['en' => 'Afghanistan' ,'ar' => 'أفغانستان'],
            'nationality' => ['en' => 'Afghan' , 'ar' =>  'أفغاني'],
        ]);


        Country::create([
            'country_code' => 'AG',
            'phone_code' => '1268',
            'name' => ['en' => 'Antigua & Barbuda' ,'ar' => 'أنتيغوا وبربودا'],
            'nationality' => ['en' => 'Antiguan/Barbudan' , 'ar' =>  'أنتيغواني/بربودي'],
        ]);


        Country::create([
            'country_code' => 'AI',
            'phone_code' => '1264',
            'name' => ['en' => 'Anguilla' ,'ar' => 'أنغويلا'],
            'nationality' => ['en' => 'Angolan' , 'ar' =>  'أنغولي'],
        ]);


        Country::create([
            'country_code' => 'AL',
            'phone_code' => '355',
            'name' => ['en' => 'Albania' ,'ar' => 'ألبانيا'],
            'nationality' => ['en' => 'Albanian' , 'ar' =>  'ألباني'],
        ]);


        Country::create([
            'country_code' => 'AM',
            'phone_code' => '374',
            'name' => ['en' => 'Armenia' ,'ar' => 'أرمينيا'],
            'nationality' => ['en' => 'Armenian' , 'ar' =>  'أرمني'],
        ]);


        Country::create([
            'country_code' => 'AO',
            'phone_code' => '244',
            'name' => ['en' => 'Angola' ,'ar' => 'أنغولا'],
            'nationality' => ['en' => 'Angolan' , 'ar' =>  'أنغولي'],
        ]);


        Country::create([
            'country_code' => 'AQ',
            'phone_code' => '672',
            'name' => ['en' => 'Antarctica' ,'ar' => 'أنتاركتيكا'],
            'nationality' => ['en' => 'Antarctic' , 'ar' =>  'أنتاركتيكي'],
        ]);


        Country::create([
            'country_code' => 'AG',
            'phone_code' => '54',
            'name' => ['en' => 'Argentina' ,'ar' => 'الأرجنتين'],
            'nationality' => ['en' => 'Argentines' , 'ar' =>  'أرجنتيني'],
        ]);


        Country::create([
            'country_code' => 'AS',
            'phone_code' => '1',
            'name' => ['en' => 'American Samoa' ,'ar' => 'ساموا الأمريكية'],
            'nationality' => ['en' => 'American Samoan' , 'ar' =>  'ساموائي أمريكي'],
        ]);


        Country::create([
            'country_code' => 'AT',
            'phone_code' => '43',
            'name' => ['en' => 'Austria' ,'ar' => 'النمسا'],
            'nationality' => ['en' => 'Austrian' , 'ar' =>  'نمساوي'],
        ]);


        Country::create([
            'country_code' => 'AU',
            'phone_code' => '61',
            'name' => ['en' => 'Australia' ,'ar' => 'أستراليا'],
            'nationality' => ['en' => 'Australian' , 'ar' =>  'أسترالي'],
        ]);


        Country::create([
            'country_code' => 'AW',
            'phone_code' => '297',
            'name' => ['en' => 'Aruba' ,'ar' => 'أروبا'],
            'nationality' => ['en' => 'Aruban' , 'ar' =>  'أروبي'],
        ]);


        Country::create([
            'country_code' => 'AX',
            'phone_code' => '358',
            'name' => ['en' => 'Åland Islands' ,'ar' => 'جزر آلاند'],
            'nationality' => ['en' => 'Åland Islander' , 'ar' =>  'أولاندي'],
        ]);


        Country::create([
            'country_code' => 'AZ',
            'phone_code' => '994',
            'name' => ['en' => 'Azerbaijan' ,'ar' => 'أذربيجان'],
            'nationality' => ['en' => 'Azerbaijani' , 'ar' =>  'أذربيجاني'],
        ]);


        Country::create([
            'country_code' => 'BA',
            'phone_code' => '387',
            'name' => ['en' => 'Bosnia & Herzegovina' ,'ar' => 'البوسنة والهرسك'],
            'nationality' => ['en' => 'Bosnian/Herzegovinian' , 'ar' =>  'بوسني/هرسكي'],
        ]);


        Country::create([
            'country_code' => 'BB',
            'phone_code' => '1246',
            'name' => ['en' => 'Barbados' ,'ar' => 'بربادوس'],
            'nationality' => ['en' => 'Barbadian' , 'ar' =>  'برباديان'],
        ]);


        Country::create([
            'country_code' => 'BD',
            'phone_code' => '880',
            'name' => ['en' => 'Bangladesh' ,'ar' => 'بنغلاديش'],
            'nationality' => ['en' => 'Bangladeshi' , 'ar' =>  'بنغالي'],
        ]);


        Country::create([
            'country_code' => 'BE',
            'phone_code' => '32',
            'name' => ['en' => 'Belgium' ,'ar' => 'بلجيكا'],
            'nationality' => ['en' => 'Belgian' , 'ar' =>  'بلجيكي'],
        ]);


        Country::create([
            'country_code' => 'BF',
            'phone_code' => '226',
            'name' => ['en' => 'Burkina Faso' ,'ar' => 'بوركينا فاسو'],
            'nationality' => ['en' => 'Burkinabe' , 'ar' =>  'بوركينابي'],
        ]);


        Country::create([
            'country_code' => 'BG',
            'phone_code' => '359',
            'name' => ['en' => 'Bulgaria' ,'ar' => 'بلغاريا'],
            'nationality' => ['en' => 'Bulgarian' , 'ar' =>  'بلغاري'],
        ]);


        Country::create([
            'country_code' => 'BH',
            'phone_code' => '973',
            'name' => ['en' => 'Bahrain' ,'ar' => 'البحرين'],
            'nationality' => ['en' => 'Bahraini' , 'ar' =>  'بحريني'],
        ]);


        Country::create([
            'country_code' => 'BI',
            'phone_code' => '257',
            'name' => ['en' => 'Burundi' ,'ar' => 'بوروندي'],
            'nationality' => ['en' => 'Burundian' , 'ar' =>  'بوروندي'],
        ]);


        Country::create([
            'country_code' => 'BJ',
            'phone_code' => '229',
            'name' => ['en' => 'Benin' ,'ar' => 'بنين'],
            'nationality' => ['en' => 'Beninese' , 'ar' =>  'بنيني'],
        ]);


        Country::create([
            'country_code' => 'BL',
            'phone_code' => '590',
            'name' => ['en' => 'St. Barthélemy' ,'ar' => 'سان بارتليمي'],
            'nationality' => ['en' => 'St. Barthélemy Islander' , 'ar' =>  'سان بارتيلمي'],
        ]);


        Country::create([
            'country_code' => 'BM',
            'phone_code' => '1441',
            'name' => ['en' => 'Bermuda' ,'ar' => 'برمودا'],
            'nationality' => ['en' => 'Bermudian' , 'ar' =>  'برمودي'],
        ]);


        Country::create([
            'country_code' => 'BN',
            'phone_code' => '673',
            'name' => ['en' => 'Brunei' ,'ar' => 'بروناي'],
            'nationality' => ['en' => 'Bruneian' , 'ar' =>  'بروناوي'],
        ]);


        Country::create([
            'country_code' => 'BO',
            'phone_code' => '591',
            'name' => ['en' => 'Bolivia' ,'ar' => 'بوليفيا'],
            'nationality' => ['en' => 'Bolivian' , 'ar' =>  'بوليفي'],
        ]);


        Country::create([
            'country_code' => 'BQ',
            'phone_code' => '599',
            'name' => ['en' => 'Caribbean Netherlands' ,'ar' => 'هولندا الكاريبية'],
            'nationality' => ['en' => 'Caribbean Netherlander' , 'ar' =>  'هولندي كاريبي'],
        ]);


        Country::create([
            'country_code' => 'BR',
            'phone_code' => '55',
            'name' => ['en' => 'Brazil' ,'ar' => 'البرازيل'],
            'nationality' => ['en' => 'Brazilian' , 'ar' =>  'برازيلي'],
        ]);


        Country::create([
            'country_code' => 'BS',
            'phone_code' => '1242',
            'name' => ['en' => 'Bahamas' ,'ar' => 'جزر البهاما'],
            'nationality' => ['en' => 'Bahamian' , 'ar' =>  'باهامي'],
        ]);


        Country::create([
            'country_code' => 'BT',
            'phone_code' => '975',
            'name' => ['en' => 'Bhutan' ,'ar' => 'بوتان'],
            'nationality' => ['en' => 'Bhutanese' , 'ar' =>  'بوتاني'],
        ]);


        Country::create([
            'country_code' => 'BV',
            'phone_code' => '47',
            'name' => ['en' => 'Bouvet Island' ,'ar' => 'جزيرة بوفيه'],
            'nationality' => ['en' => 'Bouvet Islander' , 'ar' =>  'بوفيه'],
        ]);


        Country::create([
            'country_code' => 'BW',
            'phone_code' => '267',
            'name' => ['en' => 'Botswana' ,'ar' => 'بوتسوانا'],
            'nationality' => ['en' => 'Motswana/Botswanan' , 'ar' =>  'بتسواني'],
        ]);


        Country::create([
            'country_code' => 'BY',
            'phone_code' => '375',
            'name' => ['en' => 'Belarus' ,'ar' => 'بيلاروس'],
            'nationality' => ['en' => 'Belarusian' , 'ar' =>  'بيلاروسي'],
        ]);


        Country::create([
            'country_code' => 'BZ',
            'phone_code' => '501',
            'name' => ['en' => 'Belize' ,'ar' => 'بليز'],
            'nationality' => ['en' => 'Belizean' , 'ar' =>  'بليزي'],
        ]);


        Country::create([
            'country_code' => 'CA',
            'phone_code' => '1',
            'name' => ['en' => 'Canada' ,'ar' => 'كندا'],
            'nationality' => ['en' => 'Canadian' , 'ar' =>  'كندي'],
        ]);


        Country::create([
            'country_code' => 'CC',
            'phone_code' => '61',
            'name' => ['en' => 'Cocos Keeling Islands' ,'ar' => 'جزر كوكوس كيلينغ'],
            'nationality' => ['en' => 'Cocos Islander' , 'ar' =>  'جزر كوكوسي'],
        ]);


        Country::create([
            'country_code' => 'CD',
            'phone_code' => '243',
            'name' => ['en' => 'Congo - Kinshasa' ,'ar' => 'الكونغو - كينشاسا'],
            'nationality' => ['en' => 'Congolese/Kinshasan' , 'ar' =>  'كونغولي/كينشاسي'],
        ]);


        Country::create([
            'country_code' => 'CF',
            'phone_code' => '236',
            'name' => ['en' => 'Central African Republic' ,'ar' => 'جمهورية أفريقيا الوسطى'],
            'nationality' => ['en' => 'Central African' , 'ar' =>  'وسط أفريقي'],
        ]);


        Country::create([
            'country_code' => 'CG',
            'phone_code' => '242',
            'name' => ['en' => 'Congo - Brazzaville' ,'ar' => 'الكونغو - برازافيل'],
            'nationality' => ['en' => 'Congolese/Brazzaville' , 'ar' =>  'كونغولي/برازافيلي'],
        ]);


        Country::create([
            'country_code' => 'CH',
            'phone_code' => '41',
            'name' => ['en' => 'Switzerland' ,'ar' => 'سويسرا'],
            'nationality' => ['en' => 'Swiss' , 'ar' =>  'سويسري'],
        ]);


        Country::create([
            'country_code' => 'CI',
            'phone_code' => '225',
            'name' => ['en' => 'Côte d’Ivoire' ,'ar' => 'ساحل العاج'],
            'nationality' => ['en' => 'Ivorian' , 'ar' =>  'إيفواري'],
        ]);


        Country::create([
            'country_code' => 'CK',
            'phone_code' => '682',
            'name' => ['en' => 'Cook Islands' ,'ar' => 'جزر كوك'],
            'nationality' => ['en' => 'Cook Islander' , 'ar' =>  'جزر كوكي'],
        ]);


        Country::create([
            'country_code' => 'CL',
            'phone_code' => '56',
            'name' => ['en' => 'Chile' ,'ar' => 'تشيلي'],
            'nationality' => ['en' => 'Chilean' , 'ar' =>  'تشيلي'],
        ]);


        Country::create([
            'country_code' => 'CM',
            'phone_code' => '237',
            'name' => ['en' => 'Cameroon' ,'ar' => 'الكاميرون'],
            'nationality' => ['en' => 'Cameroonian' , 'ar' =>  'كاميروني'],
        ]);


        Country::create([
            'country_code' => 'CN',
            'phone_code' => '86',
            'name' => ['en' => 'China' ,'ar' => 'الصين'],
            'nationality' => ['en' => 'Chinese' , 'ar' =>  'صيني'],
        ]);


        Country::create([
            'country_code' => 'CO',
            'phone_code' => '57',
            'name' => ['en' => 'Colombia' ,'ar' => 'كولومبيا'],
            'nationality' => ['en' => 'Colombian' , 'ar' =>  'كولومبي'],
        ]);


        Country::create([
            'country_code' => 'CR',
            'phone_code' => '506',
            'name' => ['en' => 'Costa Rica' ,'ar' => 'كوستاريكا'],
            'nationality' => ['en' => 'Costa Rican' , 'ar' =>  'كوستاريكي'],
        ]);


        Country::create([
            'country_code' => 'CU',
            'phone_code' => '53',
            'name' => ['en' => 'Cuba' ,'ar' => 'كوبا'],
            'nationality' => ['en' => 'Cuban' , 'ar' =>  'كوبي'],
        ]);


        Country::create([
            'country_code' => 'CV',
            'phone_code' => '238',
            'name' => ['en' => 'Cape Verde' ,'ar' => 'الرأس الأخضر'],
            'nationality' => ['en' => 'Cape Verdean' , 'ar' =>  'الرأس الأخضري'],
        ]);


        Country::create([
            'country_code' => 'CW',
            'phone_code' => '599',
            'name' => ['en' => 'Curaçao' ,'ar' => 'كوراساو'],
            'nationality' => ['en' => 'Curaçaoan' , 'ar' =>  'كوراساوي'],
        ]);


        Country::create([
            'country_code' => 'CX',
            'phone_code' => '61',
            'name' => ['en' => 'Christmas Island' ,'ar' => 'جزيرة كريسماس'],
            'nationality' => ['en' => 'Christmas Islander' , 'ar' =>  'جزيرة كريسماس'],
        ]);


        Country::create([
            'country_code' => 'CY',
            'phone_code' => '357',
            'name' => ['en' => 'Cyprus' ,'ar' => 'قبرص'],
            'nationality' => ['en' => 'Cypriot' , 'ar' =>  'قبرصي'],
        ]);


        Country::create([
            'country_code' => 'CZ',
            'phone_code' => '420',
            'name' => ['en' => 'Czechia' ,'ar' => 'التشيك'],
            'nationality' => ['en' => 'Czech' , 'ar' =>  'تشيكي'],
        ]);


        Country::create([
            'country_code' => 'DE',
            'phone_code' => '49',
            'name' => ['en' => 'Germany' ,'ar' => 'ألمانيا'],
            'nationality' => ['en' => 'German' , 'ar' =>  'ألماني'],
        ]);


        Country::create([
            'country_code' => 'DJ',
            'phone_code' => '253',
            'name' => ['en' => 'Djibouti' ,'ar' => 'جيبوتي'],
            'nationality' => ['en' => 'Djiboutian' , 'ar' =>  'جيبوتي'],
        ]);


        Country::create([
            'country_code' => 'DK',
            'phone_code' => '45',
            'name' => ['en' => 'Denmark' ,'ar' => 'الدانمرك'],
            'nationality' => ['en' => 'Danish' , 'ar' =>  'دانمركي'],
        ]);


        Country::create([
            'country_code' => 'DM',
            'phone_code' => '1767',
            'name' => ['en' => 'Dominica' ,'ar' => 'دومينيكا'],
            'nationality' => ['en' => 'Dominican' , 'ar' =>  'دومينيكي'],
        ]);


        Country::create([
            'country_code' => 'DO',
            'phone_code' => '1849',
            'name' => ['en' => 'Dominican Republic' ,'ar' => 'جمهورية الدومينيكان'],
            'nationality' => ['en' => 'Algerian' , 'ar' =>  'دومينيكي'],
        ]);


        Country::create([
            'country_code' => 'DZ',
            'phone_code' => '213',
            'name' => ['en' => 'Algeria' ,'ar' => 'الجزائر'],
            'nationality' => ['en' => 'Ecuadorian' , 'ar' =>  'جزائري'],
        ]);


        Country::create([
            'country_code' => 'EC',
            'phone_code' => '594',
            'name' => ['en' => 'Ecuador' ,'ar' => 'الإكوادور'],
            'nationality' => ['en' => 'Ecuadorian' , 'ar' =>  'إكوادوري'],
        ]);


        Country::create([
            'country_code' => 'EE',
            'phone_code' => '372',
            'name' => ['en' => 'Estonia' ,'ar' => 'إستونيا'],
            'nationality' => ['en' => 'Estonian' , 'ar' =>  'إستوني'],
        ]);


        Country::create([
            'country_code' => 'EG',
            'phone_code' => '20',
            'name' => ['en' => 'Egypt' ,'ar' => 'مصر'],
            'nationality' => ['en' => 'Egyptian' , 'ar' =>  'مصري'],
        ]);


        Country::create([
            'country_code' => 'EH',
            'phone_code' => '212',
            'name' => ['en' => 'Western Sahara' ,'ar' => 'الصحراء الغربية'],
            'nationality' => ['en' => 'Western Saharan' , 'ar' =>  'صحراوي غربي'],
        ]);


        Country::create([
            'country_code' => 'ER',
            'phone_code' => '291',
            'name' => ['en' => 'Eritrea' ,'ar' => 'إريتريا'],
            'nationality' => ['en' => 'Eritrean' , 'ar' =>  'إريتري'],
        ]);


        Country::create([
            'country_code' => 'ES',
            'phone_code' => '34',
            'name' => ['en' => 'Spain' ,'ar' => 'إسبانيا'],
            'nationality' => ['en' => 'Spanish' , 'ar' =>  'إسباني'],
        ]);


        Country::create([
            'country_code' => 'ET',
            'phone_code' => '251',
            'name' => ['en' => 'Ethiopia' ,'ar' => 'إثيوبيا'],
            'nationality' => ['en' => 'Ethiopian' , 'ar' =>  'إثيوبي'],
        ]);


        Country::create([
            'country_code' => 'FI',
            'phone_code' => '358',
            'name' => ['en' => 'Finland' ,'ar' => 'فنلندا'],
            'nationality' => ['en' => 'Finnish' , 'ar' =>  'فنلندي'],
        ]);


        Country::create([
            'country_code' => 'FJ',
            'phone_code' => '679',
            'name' => ['en' => 'Fiji' ,'ar' => 'فيجي'],
            'nationality' => ['en' => 'Fijian' , 'ar' =>  'فيجيان'],
        ]);


        Country::create([
            'country_code' => 'FK',
            'phone_code' => '500',
            'name' => ['en' => 'Falkland Islands' ,'ar' => 'جزر فوكلاند'],
            'nationality' => ['en' => 'Falkland Islander' , 'ar' =>  'فوكلاندي'],
        ]);


        Country::create([
            'country_code' => 'FM',
            'phone_code' => '691',
            'name' => ['en' => 'Micronesia' ,'ar' => 'ميكرونيزيا'],
            'nationality' => ['en' => 'Micronesian' , 'ar' =>  'ميكرونيزي'],
        ]);


        Country::create([
            'country_code' => 'FO',
            'phone_code' => '298',
            'name' => ['en' => 'Faroe Islands' ,'ar' => 'جزر فارو'],
            'nationality' => ['en' => 'Faroese' , 'ar' =>  'فاروي'],
        ]);


        Country::create([
            'country_code' => 'FR',
            'phone_code' => '33',
            'name' => ['en' => 'France' ,'ar' => 'فرنسا'],
            'nationality' => ['en' => 'French' , 'ar' =>  'فرنسي'],
        ]);


        Country::create([
            'country_code' => 'GA',
            'phone_code' => '241',
            'name' => ['en' => 'Gabon' ,'ar' => 'الغابون'],
            'nationality' => ['en' => 'Gabonese' , 'ar' =>  'غابوني'],
        ]);


        Country::create([
            'country_code' => 'GB',
            'phone_code' => '44',
            'name' => ['en' => 'United Kingdom' ,'ar' => 'المملكة المتحدة'],
            'nationality' => ['en' => 'British' , 'ar' =>  'بريطاني'],
        ]);


        Country::create([
            'country_code' => 'GD',
            'phone_code' => '1473',
            'name' => ['en' => 'Grenada' ,'ar' => 'غرينادا'],
            'nationality' => ['en' => 'Grenadian' , 'ar' =>  'غرينادي'],
        ]);


        Country::create([
            'country_code' => 'GE',
            'phone_code' => '995',
            'name' => ['en' => 'Georgia' ,'ar' => 'جورجيا'],
            'nationality' => ['en' => 'Georgian' , 'ar' =>  'جورجي'],
        ]);


        Country::create([
            'country_code' => 'GF',
            'phone_code' => '595',
            'name' => ['en' => 'French Guiana' ,'ar' => 'غويانا الفرنسية'],
            'nationality' => ['en' => 'French Guianese' , 'ar' =>  'غوياني فرنسي'],
        ]);


        Country::create([
            'country_code' => 'GG',
            'phone_code' => '44',
            'name' => ['en' => 'Guernsey' ,'ar' => 'غيرنزي'],
            'nationality' => ['en' => 'Guernseyian' , 'ar' =>  'غيرنزي'],
        ]);


        Country::create([
            'country_code' => 'GH',
            'phone_code' => '233',
            'name' => ['en' => 'Ghana' ,'ar' => 'غانا'],
            'nationality' => ['en' => 'Ghanaian' , 'ar' =>  'غاني '],
        ]);


        Country::create([
            'country_code' => 'GI',
            'phone_code' => '350',
            'name' => ['en' => 'Gibraltar' ,'ar' => 'جبل طارق'],
            'nationality' => ['en' => 'Gibraltarian' , 'ar' =>  'جبل طارقي'],
        ]);


        Country::create([
            'country_code' => 'GL',
            'phone_code' => '299',
            'name' => ['en' => 'Greenland' ,'ar' => 'غرينلاند'],
            'nationality' => ['en' => 'Greenlandic' , 'ar' =>  'جرينلندي'],
        ]);


        Country::create([
            'country_code' => 'GM',
            'phone_code' => '220',
            'name' => ['en' => 'Gambia' ,'ar' => 'غامبيا'],
            'nationality' => ['en' => 'Gambian' , 'ar' =>  'غامبي'],
        ]);


        Country::create([
            'country_code' => 'GN',
            'phone_code' => '224',
            'name' => ['en' => 'Guinea' ,'ar' => 'غينيا'],
            'nationality' => ['en' => 'Guinean' , 'ar' =>  'غيني'],
        ]);


        Country::create([
            'country_code' => 'GP',
            'phone_code' => '590',
            'name' => ['en' => 'Guadeloupe' ,'ar' => 'غوادلوب'],
            'nationality' => ['en' => 'Guadeloupean' , 'ar' =>  'غوادلوبي '],
        ]);


        Country::create([
            'country_code' => 'GQ',
            'phone_code' => '240',
            'name' => ['en' => 'Equatorial Guinea' ,'ar' => 'غينيا الاستوائية'],
            'nationality' => ['en' => 'Equatorial Guinean' , 'ar' =>  'غيني استوائي'],
        ]);


        Country::create([
            'country_code' => 'GR',
            'phone_code' => '30',
            'name' => ['en' => 'Greece' ,'ar' => 'اليونان'],
            'nationality' => ['en' => 'Greek' , 'ar' =>  'يوناني'],
        ]);


        Country::create([
            'country_code' => 'GS',
            'phone_code' => '500',
            'name' => ['en' => 'South Georgia & South Sandwich Islands' ,'ar' => 'جورجيا الجنوبية وجزر ساندويتش الجنوبية'],
            'nationality' => ['en' => 'South Georgian/Sandwich Islander' , 'ar' =>  'جورجي جنوبي'],
        ]);


        Country::create([
            'country_code' => 'GT',
            'phone_code' => '502',
            'name' => ['en' => 'Guatemala' ,'ar' => 'غواتيمالا'],
            'nationality' => ['en' => 'Guatemalan' , 'ar' =>  'غواتيمالي'],
        ]);


        Country::create([
            'country_code' => 'GU',
            'phone_code' => '1671',
            'name' => ['en' => 'Guam' ,'ar' => 'غوام'],
            'nationality' => ['en' => 'Guamanian' , 'ar' =>  'غوامي'],
        ]);


        Country::create([
            'country_code' => 'GW',
            'phone_code' => '245',
            'name' => ['en' => 'Guinea-Bissau' ,'ar' => 'غينيا بيساو'],
            'nationality' => ['en' => 'Bissau-Guinean' , 'ar' =>  'غيني بيساوي'],
        ]);


        Country::create([
            'country_code' => 'GY',
            'phone_code' => '593',
            'name' => ['en' => 'Guyana' ,'ar' => 'غيانا'],
            'nationality' => ['en' => 'Guyanese' , 'ar' =>  'غياني'],
        ]);


        Country::create([
            'country_code' => 'HK',
            'phone_code' => '852',
            'name' => ['en' => 'Hong Kong SAR China' ,'ar' => 'هونغ كونغ الصينية منطقة إدارية خاصة'],
            'nationality' => ['en' => 'Hong Konger' , 'ar' =>  'هونغ كونغي'],
        ]);


        Country::create([
            'country_code' => 'HM',
            'phone_code' => '672',
            'name' => ['en' => 'Heard & McDonald Islands' ,'ar' => 'جزيرة هيرد وجزر ماكدونالد'],
            'nationality' => ['en' => 'Heard & McDonald Islander' , 'ar' =>  'هيرد وماكدونالد'],
        ]);


        Country::create([
            'country_code' => 'HN',
            'phone_code' => '504',
            'name' => ['en' => 'Honduras' ,'ar' => 'هندوراس'],
            'nationality' => ['en' => 'Honduran' , 'ar' =>  'هندوراسي'],
        ]);


        Country::create([
            'country_code' => 'HR',
            'phone_code' => '385',
            'name' => ['en' => 'Croatia' ,'ar' => 'كرواتيا'],
            'nationality' => ['en' => 'Croatian' , 'ar' =>  'كرواتي'],
        ]);


        Country::create([
            'country_code' => 'HT',
            'phone_code' => '509',
            'name' => ['en' => 'Haiti' ,'ar' => 'هايتي'],
            'nationality' => ['en' => 'Haitian' , 'ar' =>  'هايتيي'],
        ]);


        Country::create([
            'country_code' => 'HU',
            'phone_code' => '36',
            'name' => ['en' => 'Hungary' ,'ar' => 'هنغاريا'],
            'nationality' => ['en' => 'Hungarian' , 'ar' =>  'هنغاري'],
        ]);


        Country::create([
            'country_code' => 'ID',
            'phone_code' => '62',
            'name' => ['en' => 'Indonesia' ,'ar' => 'إندونيسيا'],
            'nationality' => ['en' => 'Indonesian' , 'ar' =>  'إندونيسي'],
        ]);


        Country::create([
            'country_code' => 'IE',
            'phone_code' => '353',
            'name' => ['en' => 'Ireland' ,'ar' => 'أيرلندا'],
            'nationality' => ['en' => 'Irish' , 'ar' =>  'إيرلندي'],
        ]);


        Country::create([
            'country_code' => 'IL',
            'phone_code' => '972',
            'name' => ['en' => 'Israel' ,'ar' => 'إسرائيل'],
            'nationality' => ['en' => 'Israeli' , 'ar' =>  'إسرائيلي'],
        ]);


        Country::create([
            'country_code' => 'IM',
            'phone_code' => '44',
            'name' => ['en' => 'Isle of Man' ,'ar' => 'جزيرة مان'],
            'nationality' => ['en' => 'Manx' , 'ar' =>  'ماني'],
        ]);


        Country::create([
            'country_code' => 'IN',
            'phone_code' => '91',
            'name' => ['en' => 'India' ,'ar' => 'الهند'],
            'nationality' => ['en' => 'Indian' , 'ar' =>  'هندي'],
        ]);


        Country::create([
            'country_code' => 'IO',
            'phone_code' => '246',
            'name' => ['en' => 'British Indian Ocean Territory' ,'ar' => 'الإقليم البريطاني في المحيط الهندي'],
            'nationality' => ['en' => 'British Indian' , 'ar' =>  'إقليم بريطاني في المحيط الهندي'],
        ]);


        Country::create([
            'country_code' => 'IQ',
            'phone_code' => '964',
            'name' => ['en' => 'Iraq' ,'ar' => 'العراق'],
            'nationality' => ['en' => 'Iraqi' , 'ar' =>  'عراقي'],
        ]);


        Country::create([
            'country_code' => 'IR',
            'phone_code' => '98',
            'name' => ['en' => 'Iran' ,'ar' => 'إيران'],
            'nationality' => ['en' => 'Iranian' , 'ar' =>  'إيراني'],
        ]);


        Country::create([
            'country_code' => 'IS',
            'phone_code' => '354',
            'name' => ['en' => 'Iceland' ,'ar' => 'آيسلندا'],
            'nationality' => ['en' => 'Icelandic' , 'ar' =>  'آيسلندي'],
        ]);


        Country::create([
            'country_code' => 'IT',
            'phone_code' => '39',
            'name' => ['en' => 'Italy' ,'ar' => 'إيطاليا'],
            'nationality' => ['en' => 'Italian' , 'ar' =>  'إيطالي'],
        ]);


        Country::create([
            'country_code' => 'JE',
            'phone_code' => '44',
            'name' => ['en' => 'Jersey' ,'ar' => 'جيرسي'],
            'nationality' => ['en' => 'Jersey' , 'ar' =>  'جيرسي'],
        ]);


        Country::create([
            'country_code' => 'JM',
            'phone_code' => '1876',
            'name' => ['en' => 'Jamaica' ,'ar' => 'جامايكا'],
            'nationality' => ['en' => 'Jamaican' , 'ar' =>  'جامايكي'],
        ]);


        Country::create([
            'country_code' => 'JO',
            'phone_code' => '962',
            'name' => ['en' => 'Jordan' ,'ar' => 'الأردن'],
            'nationality' => ['en' => 'Jordanian' , 'ar' =>  'أردني'],
        ]);


        Country::create([
            'country_code' => 'JP',
            'phone_code' => '81',
            'name' => ['en' => 'Japan' ,'ar' => 'اليابان'],
            'nationality' => ['en' => 'Japanese' , 'ar' =>  'ياباني'],
        ]);


        Country::create([
            'country_code' => 'KE',
            'phone_code' => '254',
            'name' => ['en' => 'Kenya' ,'ar' => 'كينيا'],
            'nationality' => ['en' => 'Kenyan' , 'ar' =>  'كيني'],
        ]);


        Country::create([
            'country_code' => 'KG',
            'phone_code' => '996',
            'name' => ['en' => 'Kyrgyzstan' ,'ar' => 'قيرغيزستان'],
            'nationality' => ['en' => 'Kyrgyzstani' , 'ar' =>  'قيرغيزي'],
        ]);


        Country::create([
            'country_code' => 'KH',
            'phone_code' => '855',
            'name' => ['en' => 'Cambodia' ,'ar' => 'كمبوديا'],
            'nationality' => ['en' => 'Cambodian' , 'ar' =>  'كمبودي'],
        ]);


        Country::create([
            'country_code' => 'KI',
            'phone_code' => '686',
            'name' => ['en' => 'Kiribati' ,'ar' => 'كيريباتي'],
            'nationality' => ['en' => 'I-Kiribati' , 'ar' =>  'كيريباتي'],
        ]);


        Country::create([
            'country_code' => 'KM',
            'phone_code' => '269',
            'name' => ['en' => 'Comoros' ,'ar' => 'جزر القمر'],
            'nationality' => ['en' => 'Comoran' , 'ar' =>  'قمري'],
        ]);


        Country::create([
            'country_code' => 'KN',
            'phone_code' => '1869',
            'name' => ['en' => 'St. Kitts & Nevis' ,'ar' => 'سانت كيتس ونيفيس'],
            'nationality' => ['en' => 'Kittitian/Nevisian' , 'ar' =>  'سانت كيتس ونيفسي'],
        ]);


        Country::create([
            'country_code' => 'KP',
            'phone_code' => '850',
            'name' => ['en' => 'North Korea' ,'ar' => 'كوريا الشمالية'],
            'nationality' => ['en' => 'North Korean' , 'ar' =>  'كوري شمالي'],
        ]);


        Country::create([
            'country_code' => 'KR',
            'phone_code' => '82',
            'name' => ['en' => 'South Korea' ,'ar' => 'كوريا الجنوبية'],
            'nationality' => ['en' => 'South Korean' , 'ar' =>  'كوري جنوبي'],
        ]);


        Country::create([
            'country_code' => 'KW',
            'phone_code' => '965',
            'name' => ['en' => 'Kuwait' ,'ar' => 'الكويت'],
            'nationality' => ['en' => 'Kuwaiti' , 'ar' =>  'كويتي'],
        ]);


        Country::create([
            'country_code' => 'KY',
            'phone_code' => '1',
            'name' => ['en' => 'Cayman Islands' ,'ar' => 'جزر كايمان'],
            'nationality' => ['en' => 'Caymanian' , 'ar' =>  'كايماني'],
        ]);


        Country::create([
            'country_code' => 'KZ',
            'phone_code' => '7',
            'name' => ['en' => 'Kazakhstan' ,'ar' => 'كازاخستان'],
            'nationality' => ['en' => 'Kazakhstani' , 'ar' =>  'كازاخستاني'],
        ]);


        Country::create([
            'country_code' => 'LA',
            'phone_code' => '856',
            'name' => ['en' => 'Laos' ,'ar' => 'لاوس'],
            'nationality' => ['en' => 'Laotian' , 'ar' =>  'لاووسي'],
        ]);


        Country::create([
            'country_code' => 'LB',
            'phone_code' => '961',
            'name' => ['en' => 'Lebanon' ,'ar' => 'لبنان'],
            'nationality' => ['en' => 'Lebanese' , 'ar' =>  'لبناني'],
        ]);


        Country::create([
            'country_code' => 'LC',
            'phone_code' => '1758',
            'name' => ['en' => 'St. Lucia' ,'ar' => 'سانت لوسيا'],
            'nationality' => ['en' => 'Lucian' , 'ar' =>  'سانت لوسيان'],
        ]);


        Country::create([
            'country_code' => 'LI',
            'phone_code' => '423',
            'name' => ['en' => 'Liechtenstein' ,'ar' => 'ليختنشتاين'],
            'nationality' => ['en' => 'Liechtensteiner' , 'ar' =>  'ليختنشتايني'],
        ]);


        Country::create([
            'country_code' => 'LK',
            'phone_code' => '94',
            'name' => ['en' => 'Sri Lanka' ,'ar' => 'سريلانكا'],
            'nationality' => ['en' => 'Sri Lankan' , 'ar' =>  'سيريلانكي'],
        ]);


        Country::create([
            'country_code' => 'LR',
            'phone_code' => '231',
            'name' => ['en' => 'Liberia' ,'ar' => 'ليبيريا'],
            'nationality' => ['en' => 'Liberian' , 'ar' =>  'ليبيري'],
        ]);


        Country::create([
            'country_code' => 'LS',
            'phone_code' => '266',
            'name' => ['en' => 'Lesotho' ,'ar' => 'ليسوتو'],
            'nationality' => ['en' => 'Mosotho' , 'ar' =>  'ليسوثوي'],
        ]);


        Country::create([
            'country_code' => 'LT',
            'phone_code' => '370',
            'name' => ['en' => 'Lithuania' ,'ar' => 'ليتوانيا'],
            'nationality' => ['en' => 'Lithuanian' , 'ar' =>  'ليتواني'],
        ]);


        Country::create([
            'country_code' => 'LU',
            'phone_code' => '352',
            'name' => ['en' => 'Luxembourg' ,'ar' => 'لوكسمبورغ'],
            'nationality' => ['en' => 'Luxembourger' , 'ar' =>  'لوكسمبورجي'],
        ]);


        Country::create([
            'country_code' => 'LV',
            'phone_code' => '371',
            'name' => ['en' => 'Latvia' ,'ar' => 'لاتفيا'],
            'nationality' => ['en' => 'Latvian' , 'ar' =>  'لاتفي'],
        ]);


        Country::create([
            'country_code' => 'LY',
            'phone_code' => '218',
            'name' => ['en' => 'Libya' ,'ar' => 'ليبيا'],
            'nationality' => ['en' => 'Libyan' , 'ar' =>  'ليبي'],
        ]);


        Country::create([
            'country_code' => 'MA',
            'phone_code' => '212',
            'name' => ['en' => 'Morocco' ,'ar' => 'المغرب'],
            'nationality' => ['en' => 'Moroccan' , 'ar' =>  'مغربي'],
        ]);


        Country::create([
            'country_code' => 'MC',
            'phone_code' => '377',
            'name' => ['en' => 'Monaco' ,'ar' => 'موناكو'],
            'nationality' => ['en' => 'Monegasque' , 'ar' =>  'موناكوي'],
        ]);


        Country::create([
            'country_code' => 'MD',
            'phone_code' => '373',
            'name' => ['en' => 'Moldova' ,'ar' => 'مولدوفا'],
            'nationality' => ['en' => 'Moldovan' , 'ar' =>  'مولدوفي'],
        ]);


        Country::create([
            'country_code' => 'ME',
            'phone_code' => '382',
            'name' => ['en' => 'Montenegro' ,'ar' => 'الجبل الأسود'],
            'nationality' => ['en' => 'Montenegrin' , 'ar' =>  'جبل أسودي'],
        ]);


        Country::create([
            'country_code' => 'MF',
            'phone_code' => '590',
            'name' => ['en' => 'St. Martin' ,'ar' => 'سان مارتن'],
            'nationality' => ['en' => 'St. Martiner' , 'ar' =>  'سانت مارتيني'],
        ]);


        Country::create([
            'country_code' => 'MG',
            'phone_code' => '261',
            'name' => ['en' => 'Madagascar' ,'ar' => 'مدغشقر'],
            'nationality' => ['en' => 'Malagasy' , 'ar' =>  'مدغشقري'],
        ]);


        Country::create([
            'country_code' => 'MH',
            'phone_code' => '692',
            'name' => ['en' => 'Marshall Islands' ,'ar' => 'جزر مارشال'],
            'nationality' => ['en' => 'Marshallese' , 'ar' =>  'جزر مارشالي'],
        ]);


        Country::create([
            'country_code' => 'MK',
            'phone_code' => '389',
            'name' => ['en' => 'North Macedonia' ,'ar' => 'مقدونيا الشمالية'],
            'nationality' => ['en' => 'North Macedonian' , 'ar' =>  'مقدوني شمالي'],
        ]);


        Country::create([
            'country_code' => 'ML',
            'phone_code' => '223',
            'name' => ['en' => 'Mali' ,'ar' => 'مالي'],
            'nationality' => ['en' => 'Malian' , 'ar' =>  'مالي'],
        ]);


        Country::create([
            'country_code' => 'MM',
            'phone_code' => '95',
            'name' => ['en' => 'Myanmar Burma' ,'ar' => 'ميانمار بورما'],
            'nationality' => ['en' => 'Burmese' , 'ar' =>  'بورمي'],
        ]);


        Country::create([
            'country_code' => 'MN',
            'phone_code' => '976',
            'name' => ['en' => 'Mongolia' ,'ar' => 'منغوليا'],
            'nationality' => ['en' => 'Mongolian' , 'ar' =>  'منغولي'],
        ]);


        Country::create([
            'country_code' => 'MO',
            'phone_code' => '853',
            'name' => ['en' => 'Macao SAR China' ,'ar' => 'منطقة ماكاو الإدارية الخاصة'],
            'nationality' => ['en' => 'Macanese' , 'ar' =>  'ماكاوي'],
        ]);


        Country::create([
            'country_code' => 'MP',
            'phone_code' => '1670',
            'name' => ['en' => 'Northern Mariana Islands' ,'ar' => 'جزر ماريانا الشمالية'],
            'nationality' => ['en' => 'Northern Marianan' , 'ar' =>  'جزر ماريانا الشمالية'],
        ]);


        Country::create([
            'country_code' => 'MQ',
            'phone_code' => '596',
            'name' => ['en' => 'Martinique' ,'ar' => 'جزر المارتينيك'],
            'nationality' => ['en' => 'Martinican' , 'ar' =>  'مارتينيكي'],
        ]);


        Country::create([
            'country_code' => 'MR',
            'phone_code' => '222',
            'name' => ['en' => 'Mauritania' ,'ar' => 'موريتانيا'],
            'nationality' => ['en' => 'Mauritanian' , 'ar' =>  'موريتاني'],
        ]);


        Country::create([
            'country_code' => 'MS',
            'phone_code' => '1664',
            'name' => ['en' => 'Montserrat' ,'ar' => 'مونتسرات'],
            'nationality' => ['en' => 'Montserratian' , 'ar' =>  'مونتسراتي'],
        ]);


        Country::create([
            'country_code' => 'MT',
            'phone_code' => '356',
            'name' => ['en' => 'Malta' ,'ar' => 'مالطا'],
            'nationality' => ['en' => 'Maltese' , 'ar' =>  'مالطي'],
        ]);


        Country::create([
            'country_code' => 'MU',
            'phone_code' => '230',
            'name' => ['en' => 'Mauritius' ,'ar' => 'موريشيوس'],
            'nationality' => ['en' => 'Mauritian' , 'ar' =>  'موريشيوسي'],
        ]);


        Country::create([
            'country_code' => 'MV',
            'phone_code' => '960',
            'name' => ['en' => 'Maldives' ,'ar' => 'جزر المالديف'],
            'nationality' => ['en' => 'Maldivian' , 'ar' =>  'مالديفي'],
        ]);


        Country::create([
            'country_code' => 'MW',
            'phone_code' => '265',
            'name' => ['en' => 'Malawi' ,'ar' => 'ملاوي'],
            'nationality' => ['en' => 'Malawian' , 'ar' =>  'مالاوي'],
        ]);


        Country::create([
            'country_code' => 'MX',
            'phone_code' => '52',
            'name' => ['en' => 'Mexico' ,'ar' => 'المكسيك'],
            'nationality' => ['en' => 'Mexican' , 'ar' =>  'مكسيكي'],
        ]);


        Country::create([
            'country_code' => 'MY',
            'phone_code' => '60',
            'name' => ['en' => 'Malaysia' ,'ar' => 'ماليزيا'],
            'nationality' => ['en' => 'Malaysian' , 'ar' =>  'ماليزي'],
        ]);


        Country::create([
            'country_code' => 'MZ',
            'phone_code' => '258',
            'name' => ['en' => 'Mozambique' ,'ar' => 'موزمبيق'],
            'nationality' => ['en' => 'Mozambican' , 'ar' =>  'موزمبيقي'],
        ]);


        Country::create([
            'country_code' => 'NA',
            'phone_code' => '264',
            'name' => ['en' => 'Namibia' ,'ar' => 'ناميبيا'],
            'nationality' => ['en' => 'Namibian' , 'ar' =>  'ناميبي'],
        ]);


        Country::create([
            'country_code' => 'NC',
            'phone_code' => '687',
            'name' => ['en' => 'New Caledonia' ,'ar' => 'كاليدونيا الجديدة'],
            'nationality' => ['en' => 'Nigerien' , 'ar' =>  'كاليدوني'],
        ]);


        Country::create([
            'country_code' => 'NE',
            'phone_code' => '227',
            'name' => ['en' => 'Niger' ,'ar' => 'النيجر'],
            'nationality' => ['en' => 'Nigerien' , 'ar' =>  'نيجري'],
        ]);


        Country::create([
            'country_code' => 'NF',
            'phone_code' => '672',
            'name' => ['en' => 'Norfolk Island' ,'ar' => 'جزيرة نورفولك'],
            'nationality' => ['en' => 'Norfolk Islander' , 'ar' =>  'نورفولكي'],
        ]);


        Country::create([
            'country_code' => 'NG',
            'phone_code' => '234',
            'name' => ['en' => 'Nigeria' ,'ar' => 'نيجيريا'],
            'nationality' => ['en' => 'Nigerian' , 'ar' =>  'نيجيري'],
        ]);


        Country::create([
            'country_code' => 'NI',
            'phone_code' => '505',
            'name' => ['en' => 'Nicaragua' ,'ar' => 'نيكاراغوا'],
            'nationality' => ['en' => 'Nicaraguan' , 'ar' =>  'نيكاراجوي'],
        ]);


        Country::create([
            'country_code' => 'NL',
            'phone_code' => '31',
            'name' => ['en' => 'Netherlands' ,'ar' => 'هولندا'],
            'nationality' => ['en' => 'Dutch' , 'ar' =>  'هولندي'],
        ]);


        Country::create([
            'country_code' => 'NO',
            'phone_code' => '47',
            'name' => ['en' => 'Norway' ,'ar' => 'النرويج'],
            'nationality' => ['en' => 'Norwegian' , 'ar' =>  'نرويجي'],
        ]);


        Country::create([
            'country_code' => 'NP',
            'phone_code' => '977',
            'name' => ['en' => 'Nepal' ,'ar' => 'نيبال'],
            'nationality' => ['en' => 'Nepalese' , 'ar' =>  'نيبالي'],
        ]);


        Country::create([
            'country_code' => 'NR',
            'phone_code' => '674',
            'name' => ['en' => 'Nauru' ,'ar' => 'ناورو'],
            'nationality' => ['en' => 'Nauruan' , 'ar' =>  'ناوروي'],
        ]);


        Country::create([
            'country_code' => 'NU',
            'phone_code' => '683',
            'name' => ['en' => 'Niue' ,'ar' => 'نيوي'],
            'nationality' => ['en' => 'Niuean' , 'ar' =>  'نيويان'],
        ]);


        Country::create([
            'country_code' => 'NZ',
            'phone_code' => '64',
            'name' => ['en' => 'New Zealand' ,'ar' => 'نيوزيلندا'],
            'nationality' => ['en' => 'New Zealander' , 'ar' =>  'نيوزيلندي'],
        ]);


        Country::create([
            'country_code' => 'OM',
            'phone_code' => '968',
            'name' => ['en' => 'Oman' ,'ar' => 'عُمان'],
            'nationality' => ['en' => 'Omani' , 'ar' =>  'عُماني'],
        ]);


        Country::create([
            'country_code' => 'PA',
            'phone_code' => '507',
            'name' => ['en' => 'Panama' ,'ar' => 'بنما'],
            'nationality' => ['en' => 'Panamanian' , 'ar' =>  'بنمي'],
        ]);


        Country::create([
            'country_code' => 'PE',
            'phone_code' => '51',
            'name' => ['en' => 'Peru' ,'ar' => 'بيرو'],
            'nationality' => ['en' => 'Peruvian' , 'ar' =>  'بيروفي'],
        ]);


        Country::create([
            'country_code' => 'PF',
            'phone_code' => '689',
            'name' => ['en' => 'French Polynesia' ,'ar' => 'بولينيزيا الفرنسية'],
            'nationality' => ['en' => 'French Polynesian' , 'ar' =>  'بولينيزي فرنسي'],
        ]);


        Country::create([
            'country_code' => 'PG',
            'phone_code' => '675',
            'name' => ['en' => 'Papua New Guinea' ,'ar' => 'بابوا غينيا الجديدة'],
            'nationality' => ['en' => 'Papua New Guinean' , 'ar' =>  'بابوا غينيا الجديدة'],
        ]);


        Country::create([
            'country_code' => 'PH',
            'phone_code' => '63',
            'name' => ['en' => 'Philippines' ,'ar' => 'الفلبين'],
            'nationality' => ['en' => 'Filipino' , 'ar' =>  'فلبيني'],
        ]);


        Country::create([
            'country_code' => 'PK',
            'phone_code' => '92',
            'name' => ['en' => 'Pakistan' ,'ar' => 'باكستان'],
            'nationality' => ['en' => 'Pakistani' , 'ar' =>  'باكستاني'],
        ]);


        Country::create([
            'country_code' => 'PL',
            'phone_code' => '48',
            'name' => ['en' => 'Poland' ,'ar' => 'بولندا'],
            'nationality' => ['en' => 'Polish' , 'ar' =>  'بولندي '],
        ]);


        Country::create([
            'country_code' => 'PM',
            'phone_code' => '508',
            'name' => ['en' => 'St. Pierre & Miquelon' ,'ar' => 'سان بيير ومكويلون'],
            'nationality' => ['en' => 'St. Pierrais/Miquelonnais' , 'ar' =>  'سان بيير وميكولوني'],
        ]);


        Country::create([
            'country_code' => 'PN',
            'phone_code' => '64',
            'name' => ['en' => 'Pitcairn Islands' ,'ar' => 'جزر بيتكيرن'],
            'nationality' => ['en' => 'Puerto Rican' , 'ar' =>  'بورتوريكي'],
        ]);


        Country::create([
            'country_code' => 'PR',
            'phone_code' => '1939',
            'name' => ['en' => 'Puerto Rico' ,'ar' => 'بورتوريكو'],
            'nationality' => ['en' => 'Puerto Rican' , 'ar' =>  'بورتوريكي'],
        ]);


        Country::create([
            'country_code' => 'PS',
            'phone_code' => '970',
            'name' => ['en' => 'Palestine' ,'ar' => 'فلسطين'],
            'nationality' => ['en' => 'Palestinian' , 'ar' =>  'فلسطيني'],
        ]);


        Country::create([
            'country_code' => 'PT',
            'phone_code' => '351',
            'name' => ['en' => 'Portugal' ,'ar' => 'البرتغال'],
            'nationality' => ['en' => 'Portuguese' , 'ar' =>  'برتغالي'],
        ]);


        Country::create([
            'country_code' => 'PW',
            'phone_code' => '680',
            'name' => ['en' => 'Palau' ,'ar' => 'بالاو'],
            'nationality' => ['en' => 'Palauan' , 'ar' =>  'بالاوي'],
        ]);


        Country::create([
            'country_code' => 'PY',
            'phone_code' => '595',
            'name' => ['en' => 'Paraguay' ,'ar' => 'باراغواي'],
            'nationality' => ['en' => 'Paraguayan' , 'ar' =>  'باراغواياني'],
        ]);


        Country::create([
            'country_code' => 'QA',
            'phone_code' => '974',
            'name' => ['en' => 'Qatar' ,'ar' => 'قطر'],
            'nationality' => ['en' => 'Qatari' , 'ar' =>  'قطري'],
        ]);


        Country::create([
            'country_code' => 'RE',
            'phone_code' => '262',
            'name' => ['en' => 'Réunion' ,'ar' => 'روينيون'],
            'nationality' => ['en' => 'Réunionese' , 'ar' =>  'ريونيوني'],
        ]);


        Country::create([
            'country_code' => 'RO',
            'phone_code' => '40',
            'name' => ['en' => 'Romania' ,'ar' => 'رومانيا'],
            'nationality' => ['en' => 'Romanian' , 'ar' =>  'روماني'],
        ]);


        Country::create([
            'country_code' => 'RS',
            'phone_code' => '381',
            'name' => ['en' => 'Serbia' ,'ar' => 'صربيا'],
            'nationality' => ['en' => 'Serbian' , 'ar' =>  'صربي'],
        ]);


        Country::create([
            'country_code' => 'RU',
            'phone_code' => '7',
            'name' => ['en' => 'Russia' ,'ar' => 'روسيا'],
            'nationality' => ['en' => 'Russian' , 'ar' =>  'روسي'],
        ]);


        Country::create([
            'country_code' => 'RW',
            'phone_code' => '250',
            'name' => ['en' => 'Rwanda' ,'ar' => 'رواندا'],
            'nationality' => ['en' => 'Rwandan' , 'ar' =>  'رواندي'],
        ]);


        Country::create([
            'country_code' => 'SA',
            'phone_code' => '966',
            'name' => ['en' => 'Saudi Arabia' ,'ar' => 'المملكة العربية السعودية'],
            'nationality' => ['en' => 'Saudi Arabian' , 'ar' =>  'سعودي'],
        ]);


        Country::create([
            'country_code' => 'SB',
            'phone_code' => '677',
            'name' => ['en' => 'Solomon Islands' ,'ar' => 'جزر سليمان'],
            'nationality' => ['en' => 'Seychellois' , 'ar' =>  'جزر سليماني'],
        ]);


        Country::create([
            'country_code' => 'SC',
            'phone_code' => '248',
            'name' => ['en' => 'Seychelles' ,'ar' => 'سيشل'],
            'nationality' => ['en' => 'Seychellois' , 'ar' =>  'سيشيلي'],
        ]);


        Country::create([
            'country_code' => 'SD',
            'phone_code' => '249',
            'name' => ['en' => 'Sudan' ,'ar' => 'السودان'],
            'nationality' => ['en' => 'Sudanese' , 'ar' =>  'سوداني'],
        ]);


        Country::create([
            'country_code' => 'SE',
            'phone_code' => '46',
            'name' => ['en' => 'Sweden' ,'ar' => 'السويد'],
            'nationality' => ['en' => 'Swedish' , 'ar' =>  'سويدي'],
        ]);


        Country::create([
            'country_code' => 'SG',
            'phone_code' => '65',
            'name' => ['en' => 'Singapore' ,'ar' => 'سنغافورة'],
            'nationality' => ['en' => 'Singaporean' , 'ar' =>  'سنغافوري'],
        ]);


        Country::create([
            'country_code' => 'SH',
            'phone_code' => '290',
            'name' => ['en' => 'St. Helena' ,'ar' => 'سانت هيلينا'],
            'nationality' => ['en' => 'St. Helenian' , 'ar' =>  'سانت هيليني'],
        ]);


        Country::create([
            'country_code' => 'SI',
            'phone_code' => '386',
            'name' => ['en' => 'Slovenia' ,'ar' => 'سلوفينيا'],
            'nationality' => ['en' => 'Slovenian' , 'ar' =>  'سلوفيني'],
        ]);


        Country::create([
            'country_code' => 'SJ',
            'phone_code' => '47',
            'name' => ['en' => 'Svalbard & Jan Mayen' ,'ar' => 'سفالبارد وجان ماين'],
            'nationality' => ['en' => 'Svalbardian' , 'ar' =>  'سفالباردي'],
        ]);


        Country::create([
            'country_code' => 'SK',
            'phone_code' => '421',
            'name' => ['en' => 'Slovakia' ,'ar' => 'سلوفاكيا'],
            'nationality' => ['en' => 'Slovakian' , 'ar' =>  'سلوفاكي'],
        ]);


        Country::create([
            'country_code' => 'SL',
            'phone_code' => '232',
            'name' => ['en' => 'Sierra Leone' ,'ar' => 'سيراليون'],
            'nationality' => ['en' => 'Sierra Leonean' , 'ar' =>  'سيراليوني'],
        ]);


        Country::create([
            'country_code' => 'SM',
            'phone_code' => '378',
            'name' => ['en' => 'San Marino' ,'ar' => 'سان مارينو'],
            'nationality' => ['en' => 'San Marinese' , 'ar' =>  'سان مارينيز'],
        ]);


        Country::create([
            'country_code' => 'SN',
            'phone_code' => '221',
            'name' => ['en' => 'Senegal' ,'ar' => 'السنغال'],
            'nationality' => ['en' => 'Senegalese' , 'ar' =>  'سنغالي'],
        ]);


        Country::create([
            'country_code' => 'SO',
            'phone_code' => '252',
            'name' => ['en' => 'Somalia' ,'ar' => 'الصومال'],
            'nationality' => ['en' => 'Somali' , 'ar' =>  'صومالي'],
        ]);


        Country::create([
            'country_code' => 'SR',
            'phone_code' => '597',
            'name' => ['en' => 'Suriname' ,'ar' => 'سورينام'],
            'nationality' => ['en' => 'Surinamese' , 'ar' =>  'سورينامي'],
        ]);


        Country::create([
            'country_code' => 'SS',
            'phone_code' => '211',
            'name' => ['en' => 'South Sudan' ,'ar' => 'جنوب السودان'],
            'nationality' => ['en' => 'South Sudanese' , 'ar' =>  'جنوب السوداني'],
        ]);


        Country::create([
            'country_code' => 'ST',
            'phone_code' => '239',
            'name' => ['en' => 'São Tomé & Príncipe' ,'ar' => 'ساو تومي وبرينسيبي'],
            'nationality' => ['en' => 'São Toméan/Príncipian' , 'ar' =>  'ساو توميان وبرينسيبي'],
        ]);


        Country::create([
            'country_code' => 'SV',
            'phone_code' => '503',
            'name' => ['en' => 'El Salvador' ,'ar' => 'السلفادور'],
            'nationality' => ['en' => 'Salvadoran' , 'ar' =>  'سلفادوري'],
        ]);


        Country::create([
            'country_code' => 'SX',
            'phone_code' => '599',
            'name' => ['en' => 'Sint Maarten' ,'ar' => 'سانت مارتن'],
            'nationality' => ['en' => 'Sint Maartener' , 'ar' =>  'سانت مارتني'],
        ]);


        Country::create([
            'country_code' => 'SY',
            'phone_code' => '963',
            'name' => ['en' => 'Syria' ,'ar' => 'سوريا'],
            'nationality' => ['en' => 'Syrian' , 'ar' =>  'سوري'],
        ]);


        Country::create([
            'country_code' => 'SZ',
            'phone_code' => '268',
            'name' => ['en' => 'Eswatini' ,'ar' => 'إسواتيني'],
            'nationality' => ['en' => 'Eswatini/Swazi' , 'ar' =>  'إسواتيني'],
        ]);


        Country::create([
            'country_code' => 'TC',
            'phone_code' => '1649',
            'name' => ['en' => 'Turks & Caicos Islands' ,'ar' => 'جزر توركس وكايكوس'],
            'nationality' => ['en' => 'Turks and Caicos Islander' , 'ar' =>  'تركس وكايكوسي'],
        ]);


        Country::create([
            'country_code' => 'TD',
            'phone_code' => '235',
            'name' => ['en' => 'Chad' ,'ar' => 'تشاد'],
            'nationality' => ['en' => 'Chadian' , 'ar' =>  'تشادي'],
        ]);


        Country::create([
            'country_code' => 'TF',
            'phone_code' => '262',
            'name' => ['en' => 'French Southern Territories' ,'ar' => 'الأقاليم الجنوبية الفرنسية'],
            'nationality' => ['en' => 'French Southern and Antarctic Territories' , 'ar' =>  'فرنسي جنوبي'],
        ]);


        Country::create([
            'country_code' => 'TG',
            'phone_code' => '228',
            'name' => ['en' => 'Togo' ,'ar' => 'توغو'],
            'nationality' => ['en' => 'Togolese' , 'ar' =>  'توغولي'],
        ]);


        Country::create([
            'country_code' => 'TH',
            'phone_code' => '66',
            'name' => ['en' => 'Thailand' ,'ar' => 'تايلاند'],
            'nationality' => ['en' => 'Thai' , 'ar' =>  'تايلندي'],
        ]);


        Country::create([
            'country_code' => 'TJ',
            'phone_code' => '992',
            'name' => ['en' => 'Tajikistan' ,'ar' => 'طاجيكستان'],
            'nationality' => ['en' => 'Tajikistani' , 'ar' =>  'طاجيكي'],
        ]);


        Country::create([
            'country_code' => 'TK',
            'phone_code' => '690',
            'name' => ['en' => 'Tokelau' ,'ar' => 'توكيلو'],
            'nationality' => ['en' => 'Tokelauan' , 'ar' =>  'توكيلوي'],
        ]);


        Country::create([
            'country_code' => 'TL',
            'phone_code' => '670',
            'name' => ['en' => 'Timor-Leste' ,'ar' => 'تيمور - ليشتي'],
            'nationality' => ['en' => 'East Timorese' , 'ar' =>  'تيموري'],
        ]);


        Country::create([
            'country_code' => 'TM',
            'phone_code' => '993',
            'name' => ['en' => 'Turkmenistan' ,'ar' => 'تركمانستان'],
            'nationality' => ['en' => 'Turkmen' , 'ar' =>  'تركماني'],
        ]);


        Country::create([
            'country_code' => 'TN',
            'phone_code' => '216',
            'name' => ['en' => 'Tunisia' ,'ar' => 'تونس'],
            'nationality' => ['en' => 'Tunisian' , 'ar' =>  'تونسي'],
        ]);


        Country::create([
            'country_code' => 'TO',
            'phone_code' => '676',
            'name' => ['en' => 'Tonga' ,'ar' => 'تونغا'],
            'nationality' => ['en' => 'Tongan' , 'ar' =>  'تونغي'],
        ]);


        Country::create([
            'country_code' => 'TR',
            'phone_code' => '90',
            'name' => ['en' => 'Turkey' ,'ar' => 'تركيا'],
            'nationality' => ['en' => 'Turkish' , 'ar' =>  'تركي'],
        ]);


        Country::create([
            'country_code' => 'TT',
            'phone_code' => '1868',
            'name' => ['en' => 'Trinidad & Tobago' ,'ar' => 'ترينيداد وتوباغو'],
            'nationality' => ['en' => 'Trinidadian' , 'ar' =>  'ترينيدادي'],
        ]);


        Country::create([
            'country_code' => 'TV',
            'phone_code' => '688',
            'name' => ['en' => 'Tuvalu' ,'ar' => 'توفالو'],
            'nationality' => ['en' => 'Tuvaluan' , 'ar' =>  'توفالوي'],
        ]);


        Country::create([
            'country_code' => 'TW',
            'phone_code' => '886',
            'name' => ['en' => 'Taiwan' ,'ar' => 'تايوان'],
            'nationality' => ['en' => 'Taiwanese' , 'ar' =>  'تايواني'],
        ]);


        Country::create([
            'country_code' => 'TZ',
            'phone_code' => '255',
            'name' => ['en' => 'Tanzania' ,'ar' => 'تنزانيا'],
            'nationality' => ['en' => 'Tanzanian' , 'ar' =>  'تنزاني'],
        ]);


        Country::create([
            'country_code' => 'UA',
            'phone_code' => '380',
            'name' => ['en' => 'Ukraine' ,'ar' => 'أوكرانيا'],
            'nationality' => ['en' => 'Ukrainian' , 'ar' =>  'أوكراني'],
        ]);


        Country::create([
            'country_code' => 'UG',
            'phone_code' => '256',
            'name' => ['en' => 'Uganda' ,'ar' => 'أوغندا'],
            'nationality' => ['en' => 'Ugandan' , 'ar' =>  'أوغندي'],
        ]);


        Country::create([
            'country_code' => 'UM',
            'phone_code' => '246',
            'name' => ['en' => 'U.S. Outlying Islands' ,'ar' => 'جزر الولايات المتحدة النائية'],
            'nationality' => ['en' => 'American' , 'ar' =>  'أمريكي'],
        ]);


        Country::create([
            'country_code' => 'US',
            'phone_code' => '1',
            'name' => ['en' => 'United States' ,'ar' => 'الولايات المتحدة'],
            'nationality' => ['en' => 'American' , 'ar' =>  'أمريكي'],
        ]);


        Country::create([
            'country_code' => 'UY',
            'phone_code' => '598',
            'name' => ['en' => 'Uruguay' ,'ar' => 'أورغواي'],
            'nationality' => ['en' => 'Uruguayan' , 'ar' =>  'أوروغوياني'],
        ]);


        Country::create([
            'country_code' => 'UZ',
            'phone_code' => '998',
            'name' => ['en' => 'Uzbekistan' ,'ar' => 'أوزبكستان'],
            'nationality' => ['en' => 'Uzbekistani' , 'ar' =>  'أوزبكي'],
        ]);


        Country::create([
            'country_code' => 'VE',
            'phone_code' => '58',
            'name' => ['en' => 'Venezuelan' ,'ar' => 'فنزويلا'],
            'nationality' => ['en' => 'British Virgin Islands' , 'ar' =>  'فنزويلي'],
        ]);


        Country::create([
            'country_code' => 'VN',
            'phone_code' => '84',
            'name' => ['en' => 'Vietnam' ,'ar' => 'فيتنام'],
            'nationality' => ['en' => 'Vietnamese' , 'ar' =>  'فيتنامي'],
        ]);


        Country::create([
            'country_code' => 'AS',
            'phone_code' => '1',
            'name' => ['en' => 'American Samoans' ,'ar' => 'ساموا'],
            'nationality' => ['en' => 'Samoa' , 'ar' =>  'ساموي'],
        ]);


        Country::create([
            'country_code' => 'XK',
            'phone_code' => '383',
            'name' => ['en' => 'Kosovo' ,'ar' => 'كوسوفو'],
            'nationality' => ['en' => 'Kosovar' , 'ar' =>  'كوسوفي'],
        ]);


        Country::create([
            'country_code' => 'YE',
            'phone_code' => '967',
            'name' => ['en' => 'Yemen' ,'ar' => 'اليمن'],
            'nationality' => ['en' => 'Yemeni' , 'ar' =>  'يمني'],
        ]);


        Country::create([
            'country_code' => 'YT',
            'phone_code' => '262',
            'name' => ['en' => 'Mayotte' ,'ar' => 'مايوت'],
            'nationality' => ['en' => 'Mayotte' , 'ar' =>  'مايوتي'],
        ]);


        Country::create([
            'country_code' => 'ZA',
            'phone_code' => '27',
            'name' => ['en' => 'South Africa' ,'ar' => 'جنوب أفريقيا'],
            'nationality' => ['en' => 'South Africa' , 'ar' =>  'جنوب أفريقي'],
        ]);


        Country::create([
            'country_code' => 'ZM',
            'phone_code' => '260',
            'name' => ['en' => 'Zambia' ,'ar' => 'زامبيا'],
            'nationality' => ['en' => 'Zimbabwean' , 'ar' =>  'زامبي'],
        ]);


        Country::create([
            'country_code' => 'ZW',
            'phone_code' => '263',
            'name' => ['en' => 'Zimbabwe' ,'ar' => 'زيمبابوي'],
            'nationality' => ['en' => 'Zimbabwean' , 'ar' =>  'زمبابوي'],
        ]);


}
 }

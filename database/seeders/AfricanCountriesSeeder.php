<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AfricanCountriesSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        //
    }
}

/*
INSERT INTO countries (name_en, name_fr, name_ar, iso2, iso3, phone_code, flag, latitude, longitude) VALUES
('Algeria', 'Algérie', 'الجزائر', 'DZ', 'DZA', '+213', 'dz.png', 28.0339, 1.6596),
('Angola', 'Angola', 'أنغولا', 'AO', 'AGO', '+244', 'ao.png', -11.2027, 17.8739),
('Benin', 'Bénin', 'بنين', 'BJ', 'BEN', '+229', 'bj.png', 9.3077, 2.3158),
('Botswana', 'Botswana', 'بوتسوانا', 'BW', 'BWA', '+267', 'bw.png', -22.3285, 24.6849),
('Burkina Faso', 'Burkina Faso', 'بوركينا فاسو', 'BF', 'BFA', '+226', 'bf.png', 12.2383, -1.5616),
('Burundi', 'Burundi', 'بوروندي', 'BI', 'BDI', '+257', 'bi.png', -3.3731, 29.9189),
('Cabo Verde', 'Cap-Vert', 'الرأس الأخضر', 'CV', 'CPV', '+238', 'cv.png', 16.5388, -23.0418),
('Cameroon', 'Cameroun', 'الكاميرون', 'CM', 'CMR', '+237', 'cm.png', 7.3697, 12.3547),
('Central African Republic', 'République centrafricaine', 'جمهورية أفريقيا الوسطى', 'CF', 'CAF', '+236', 'cf.png', 6.6111, 20.9394),
('Chad', 'Tchad', 'تشاد', 'TD', 'TCD', '+235', 'td.png', 15.4542, 18.7322),
('Comoros', 'Comores', 'جزر القمر', 'KM', 'COM', '+269', 'km.png', -11.8750, 43.8722),
('Congo (Brazzaville)', 'Congo (Brazzaville)', 'الكونغو', 'CG', 'COG', '+242', 'cg.png', -0.2280, 15.8277),
('Congo (Kinshasa)', 'Congo (Kinshasa)', 'جمهورية الكونغو الديمقراطية', 'CD', 'COD', '+243', 'cd.png', -4.0383, 21.7587),
('Djibouti', 'Djibouti', 'جيبوتي', 'DJ', 'DJI', '+253', 'dj.png', 11.8251, 42.5903),
('Egypt', 'Égypte', 'مصر', 'EG', 'EGY', '+20', 'eg.png', 26.8206, 30.8025),
('Equatorial Guinea', 'Guinée équatoriale', 'غينيا الاستوائية', 'GQ', 'GNQ', '+240', 'gq.png', 1.6508, 10.2679),
('Eritrea', 'Érythrée', 'إريتريا', 'ER', 'ERI', '+291', 'er.png', 15.1794, 39.7823),
('Eswatini', 'Eswatini', 'إسواتيني', 'SZ', 'SWZ', '+268', 'sz.png', -26.5225, 31.4659),
('Ethiopia', 'Éthiopie', 'إثيوبيا', 'ET', 'ETH', '+251', 'et.png', 9.1450, 40.4897),
('Gabon', 'Gabon', 'الغابون', 'GA', 'GAB', '+241', 'ga.png', -0.8037, 11.6094),
('Gambia', 'Gambie', 'غامبيا', 'GM', 'GMB', '+220', 'gm.png', 13.4432, -15.3101),
('Ghana', 'Ghana', 'غانا', 'GH', 'GHA', '+233', 'gh.png', 7.9465, -1.0232),
('Guinea', 'Guinée', 'غينيا', 'GN', 'GIN', '+224', 'gn.png', 9.9456, -9.6966),
('Guinea-Bissau', 'Guinée-Bissau', 'غينيا بيساو', 'GW', 'GNB', '+245', 'gw.png', 11.8037, -15.1804),
('Ivory Coast', "Côte d'Ivoire", 'ساحل العاج', 'CI', 'CIV', '+225', 'ci.png', 7.5400, -5.5471),
('Kenya', 'Kenya', 'كينيا', 'KE', 'KEN', '+254', 'ke.png', -0.0236, 37.9062),
('Lesotho', 'Lesotho', 'ليسوتو', 'LS', 'LSO', '+266', 'ls.png', -29.6100, 28.2336),
('Liberia', 'Libéria', 'ليبيريا', 'LR', 'LBR', '+231', 'lr.png', 6.4281, -9.4295),
('Libya', 'Libye', 'ليبيا', 'LY', 'LBY', '+218', 'ly.png', 26.3351, 17.2283),
('Madagascar', 'Madagascar', 'مدغشقر', 'MG', 'MDG', '+261', 'mg.png', -18.7669, 46.8691),
('Malawi', 'Malawi', 'مالاوي', 'MW', 'MWI', '+265', 'mw.png', -13.2543, 34.3015),
('Mali', 'Mali', 'مالي', 'ML', 'MLI', '+223', 'ml.png', 17.5707, -3.9962),
('Mauritania', 'Mauritanie', 'موريتانيا', 'MR', 'MRT', '+222', 'mr.png', 21.0079, -10.9408),
('Mauritius', 'Maurice', 'موريشيوس', 'MU', 'MUS', '+230', 'mu.png', -20.3484, 57.5522),
('Morocco', 'Maroc', 'المغرب', 'MA', 'MAR', '+212', 'ma.png', 31.7917, -7.0926),
('Mozambique', 'Mozambique', 'موزمبيق', 'MZ', 'MOZ', '+258', 'mz.png', -18.6657, 35.5296),
('Namibia', 'Namibie', 'ناميبيا', 'NA', 'NAM', '+264', 'na.png', -22.5597, 17.0832),
('Niger', 'Niger', 'النيجر', 'NE', 'NER', '+227', 'ne.png', 17.6078, 8.0817),
('Nigeria', 'Nigéria', 'نيجيريا', 'NG', 'NGA', '+234', 'ng.png', 9.0820, 8.6753),
('Rwanda', 'Rwanda', 'رواندا', 'RW', 'RWA', '+250', 'rw.png', -1.9403, 29.8739),
('Sao Tome and Principe', 'Sao Tomé-et-Principe', 'ساو تومي وبرينسيبي', 'ST', 'STP', '+239', 'st.png', 0.1864, 6.6131),
('Senegal', 'Sénégal', 'السنغال', 'SN', 'SEN', '+221', 'sn.png', 14.4974, -14.4524),
('Seychelles', 'Seychelles', 'سيشل', 'SC', 'SYC', '+248', 'sc.png', -4.6796, 55.4915),
('Sierra Leone', 'Sierra Leone', 'سيراليون', 'SL', 'SLE', '+232', 'sl.png', 8.4606, -11.7799),
('Somalia', 'Somalie', 'الصومال', 'SO', 'SOM', '+252', 'so.png', 5.1521, 46.1996),
('South Africa', 'Afrique du Sud', 'جنوب أفريقيا', 'ZA', 'ZAF', '+27', 'za.png', -30.5595, 22.9375),
('South Sudan', 'Soudan du Sud', 'جنوب السودان', 'SS', 'SSD', '+211', 'ss.png', 6.8770, 31.3070),
('Sudan', 'Soudan', 'السودان', 'SD', 'SDN', '+249', 'sd.png', 12.8628, 30.2176),
('Tanzania', 'Tanzanie', 'تنزانيا', 'TZ', 'TZA', '+255', 'tz.png', -6.3690, 34.8888),
('Togo', 'Togo', 'توغو', 'TG', 'TGO', '+228', 'tg.png', 8.6195, 0.8248),
('Tunisia', 'Tunisie', 'تونس', 'TN', 'TUN', '+216', 'tn.png', 33.8869, 9.5375),
('Uganda', 'Ouganda', 'أوغندا', 'UG', 'UGA', '+256', 'ug.png', 1.3733, 32.2903),
('Western Sahara', 'Sahara occidental', 'الصحراء الغربية', 'EH', 'ESH', '+212', 'eh.png', 24.2155, -12.8858),
('Zambia', 'Zambie', 'زامبيا', 'ZM', 'ZMB', '+260', 'zm.png', -13.1339, 27.8493),
('Zimbabwe', 'Zimbabwe', 'زيمبابوي', 'ZW', 'ZWE', '+263', 'zw.png', -19.0154, 29.1549),
('Other', 'Autre', 'آخر', 'WW', 'WWW', '+0', 'ww.png', 0.0, 0.0);
*/
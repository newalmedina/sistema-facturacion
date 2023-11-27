<?php

namespace Database\Seeders;

use App\Models\Diagnosi;
use App\Models\MedicalSpecialization;
use App\Models\Municipio;
use App\Models\Province;
use Illuminate\Database\Seeder;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class DiagnosisDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {

            $data = [
                "Resfriado común (catarro)", "Gripe (influenza)", "Hipertensión arterial", "Diabetes tipo 2", "Colesterol alto (hipercolesterolemia)", "Dolor de cabeza (cefalea)", "Migraña", "Asma", "Enfermedad pulmonar obstructiva crónica (EPOC)", "Alergias", "Gastritis", "Reflujo gastroesofágico (ERGE)", "Úlcera gástrica o duodenal", "Artritis", "Osteoporosis", "Depresión", "Ansiedad", "Trastorno del sueño", "Síndrome del intestino irritable (SII)", "Enfermedad cardiovascular", "Enfermedad renal crónica", "Enfermedad de tiroides", "Obesidad", "Enfermedad hepática grasa no alcohólica (EHGNA)", "Anemia", "Acné", "Infecciones del tracto urinario (ITU)", "Infección por levaduras (candidiasis)", "Sinusitis", "Enfermedad de Crohn", "Colitis ulcerosa", "Bronquitis", "Neumonía", "Eczema", "Psoriasis", "Hemorroides", "Enfermedad celíaca", "Enfermedad de Parkinson", "Enfermedad de Alzheimer", "Cáncer de mama", "Cáncer de próstata", "Cáncer de pulmón", "Cáncer colorrectal", "Cáncer de piel (melanoma)", "VIH / Sida", "Cáncer de útero (cervical)", "Cáncer de vejiga", "Cáncer de riñón", "Cáncer de hígado", "Cáncer de páncreas", "Cáncer de ovario", "Cáncer de tiroides", "Cáncer de estómago", "Cáncer de esófago", "Cáncer de cabeza y cuello", "Cáncer de testículo", "Cáncer de leucemia", "Cáncer de linfoma", "Fibromialgia", "Enfermedad venosa crónica", "Enfermedad arterial periférica", "Anorexia nerviosa", "Bulimia nerviosa", "Trastorno por déficit de atención e hiperactividad (TDAH)", "Autismo", "Esquizofrenia", "Trastorno bipolar", "Trastorno límite de la personalidad (TLP)", "Enfermedad de Lyme", "Herpes labial (herpes simple)", "Enfermedad de Raynaud", "Gota", "Rosácea", "Síndrome del túnel carpiano", "Glaucoma", "Cataratas", "Conjuntivitis", "Degeneración macular relacionada con la edad (DMRE)", "Alergia ocular", "Enfermedad de la vesícula biliar", "Cálculos renales", "Insuficiencia cardíaca congestiva", "Arritmia cardíaca", "Accidente cerebrovascular (ACV)", "Enfermedad arterial coronaria", "Enfermedad de la válvula cardíaca", "Trombosis venosa profunda (TVP)", "Hipertiroidismo", "Hipotiroidismo", "Enfermedad de Addison", "Enfermedad de Cushing", "Enfermedad de Graves", "Enfermedad de Hashimoto", "Diabetes tipo 1", "Síndrome de ovario poliquístico (SOP)", "Endometriosis", "Menopausia", "Síndrome de fatiga crónica", "Síndrome de intestino permeable", "Síndrome metabólico", "Síndrome de apnea del sueño", "Neumonía atípica", "Hepatitis viral", "Hipotiroidismo primario", "Enfermedad de Ménière", "Enfermedad de Huntington", "Esclerosis múltiple", "Síndrome de Guillain-Barré", "Lupus eritematoso sistémico", "Espondilitis anquilosante", "Síndrome de Sjögren", "Esclerodermia", "Leucemia linfocítica crónica", "Leucemia mieloide aguda", "Enfermedad de Gaucher", "Enfermedad de Fabry", "Síndrome de Marfan", "Síndrome de Down", "Síndrome de Turner", "Síndrome de Klinefelter", "Deficiencia de vitamina B12", "Deficiencia de hierro", "Anorexia nerviosa", "Bulimia nerviosa", "Trastorno de estrés postraumático (TEPT)", "Trastorno de ansiedad social", "Trastorno de pánico", "Trastorno de personalidad antisocial", "Trastorno de personalidad narcisista", "Trastorno de personalidad esquizoide", "Trastorno de personalidad obsesivo-compulsivo", "Trastorno de personalidad esquizotípico", "Hemofilia", "Trombocitopenia", "Fibrosis quística", "Enfermedad de von Willebrand", "Enfermedad inflamatoria intestinal", "Enfermedad de Behçet", "Enfermedad de Kawasaki", "Enfermedad de Chagas", "Enfermedad de Lyme", "Enfermedad de Castleman", "Síndrome nefrótico", "Nefritis lúpica", "Insuficiencia renal aguda", "Insuficiencia renal crónica", "Glomerulonefritis", "Síndrome de Budd-Chiari", "Hiperplasia prostática benigna", "Enfermedad de Peyronie", "Endometriosis", "Adenomiosis", "Mioma uterino", "Cáncer de endometrio", "Cáncer de cuello uterino", "Cáncer de ovario", "Cáncer de próstata", "Cáncer de testículo", "Cáncer de páncreas", "Cáncer de hígado", "Cáncer de vejiga", "Cáncer de esófago", "Cáncer de estómago", "Cáncer de tiroides", "Cáncer de cabeza y cuello", "Cáncer de cerebro", "Linfoma de Hodgkin", "Linfoma no Hodgkin", "Mieloma múltiple", "Tumor de Wilms", "Neuroblastoma", "Retinoblastoma", "Carcinoma de células escamosas", "Carcinoma de células basales", "Carcinoma de pulmón de células pequeñas", "Carcinoma de pulmón de células no pequeñas", "Melanoma", "Osteosarcoma", "Sarcoma de Ewing", "Rabdomiosarcoma", "Leiomiosarcoma", "Liposarcoma", "Rabdomiosarcoma", "Condrosarcoma", "Osteoartritis", "Artritis reumatoide", "Espondilitis anquilosante", "Gota", "Síndrome del túnel carpiano", "Osteoporosis", "Fibromialgia", "Poliomielitis", "Encefalitis", "Meningitis", "Esclerosis lateral amiotrófica (ELA)", "Enfermedad de Parkinson", "Trastorno del espectro autista (TEA)", "Trastorno por déficit de atención e hiperactividad (TDAH)", "Síndrome de Tourette", "Trastorno bipolar", "Esquizofrenia", "Parálisis cerebral", "Epilepsia", "Enfermedad de Wilson", "Enfermedad de Buerger", "Enfermedad de Raynaud", "Enfermedad de von Hippel-Lindau", "Hemangioma", "Queratosis actínica", "Pénfigo", "Escleritis", "Uveítis", "Síndrome de fatiga crónica", "Hepatitis autoinmune", "Enfermedad de Addison", "Síndrome de Cushing", "Hiperaldosteronismo", "Feocromocitoma", "Diabetes insípida", "Hipopituitarismo", "Síndrome de Conn", "Osteomalacia", "Síndrome de Reye", "Mononucleosis infecciosa", "Síndrome de Stevens-Johnson", "Síndrome de ovario poliquístico (SOP)", "Síndrome de hiperestimulación ovárica", "Displasia de cadera", "Luxación congénita de cadera", "Distrofia muscular de Duchenne", "Escoliosis", "Poliomielitis", "Osteogénesis imperfecta", "Síndrome de Rett", "Síndrome de Turner", "Síndrome de Klinefelter", "Síndrome de Down", "Síndrome de Edwards", "Síndrome de Patau", "Síndrome de Angelman", "Síndrome de Prader-Willi", "Síndrome de Williams", "Síndrome de Apert", "Síndrome de Treacher Collins", "Síndrome de Goldenhar", "Síndrome de Alport", "Síndrome de Goodpasture", "Síndrome de Gitelman", "Síndrome de Bartter", "Síndrome de Marfan", "Síndrome de Ehlers-Danlos", "Síndrome de Klippel-Trénaunay", "Síndrome de Poland", "Síndrome de Sotos", "Síndrome de Mowat-Wilson", "Síndrome de Down", "Síndrome de Cat Eye", "Síndrome de Prader-Willi", "Síndrome de DiGeorge", "Síndrome de Moebius", "Síndrome de Charcot-Marie-Tooth", "Síndrome de Guillain-Barré", "Síndrome de Miller Fisher", "Síndrome de Horner", "Síndrome de Ramsay Hunt", "Síndrome de Brugada", "Síndrome de Long QT", "Síndrome de Wolff-Parkinson-White", "Síndrome de Dressler", "Síndrome de Takotsubo", "Síndrome de hiperviscosidad", "Síndrome nefrótico", "Síndrome hemolítico urémico", "Síndrome de antifosfolípidos", "Síndrome mielodisplásico", "Síndrome de Tourette", "Síndrome de Kleine-Levin", "Síndrome de Munchausen", "Síndrome de Munchausen por poderes", "Síndrome de Stockholm", "Síndrome de abstinencia neonatal", "Síndrome de abstinencia de alcohol", "Síndrome de abstinencia de opioides", "Síndrome de abstinencia de benzodiacepinas", "Síndrome de estrés posvacacional", "Síndrome de desgaste profesional (burnout)", "Síndrome de piernas inquietas", "Síndrome de abstinencia de cafeína", "Síndrome de abstinencia de nicotina", "Síndrome del nido vacío", "Síndrome de abstinencia digital (adicción a dispositivos)", "Síndrome de abstinencia de redes sociales", "Síndrome del ojo seco", "Síndrome de abstinencia de azúcar", "Síndrome de apnea del sueño", "Síndrome de abstinencia de alimentos procesados", "Síndrome de piernas pesadas", "Síndrome de Rapunzel (tricofagia)", "Síndrome de Rapunzel (tricotilomanía)", "Síndrome de Rapunzel (tricobezoar)", "Síndrome de Rapunzel (tricorrhexis nodosa)"
            ];

            foreach ($data as $value) {
                $specialization = new Diagnosi();
                $specialization->name = ucfirst($value);
                $specialization->active = 1;
                $specialization->save();
            }


            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            // Manejar errores en caso de que la solicitud falle

        }
    }
}

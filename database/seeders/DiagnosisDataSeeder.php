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

                "Resfriado común", 'Cefalea',, "VIH/Sida", "Gripe", "Hipertensión arterial", "Diabetes tipo 1", "Diabetes tipo 2", "Diabetes gestacional", "Asma", "Enfermedad cardíaca coronaria", "Depresión", "Ansiedad", "Artritis", "Migraña", "Enfermedad pulmonar obstructiva crónica (EPOC)", "Alergias", "Enfermedad de Alzheimer", "Reflujo gastroesofágico (ERGE)", "Hipotiroidismo", "Cáncer (como cáncer de mama, cáncer de pulmón, cáncer colorrectal)", "Úlcera péptica", "Insomnio", "Osteoporosis", "Infección del tracto urinario (ITU)", "Trastorno del sueño apnea obstructiva del sueño (SAOS)", "Anemia", "Enfermedad renal crónica", "Síndrome del intestino irritable (SII)", "Enfermedad inflamatoria intestinal", "Esclerosis múltiple", "Lupus eritematoso sistémico", "Trastorno bipolar", "Trastorno por déficit de atención e hiperactividad (TDAH)", "Fibromialgia", "Enfermedad de Parkinson", "Esquizofrenia", "Epilepsia", "Enfermedad hepática grasa no alcohólica (EHGNA)", "Endometriosis", "Enfermedad celíaca", "Síndrome de ovario poliquístico (SOP)", "EPOC (Enfermedad Pulmonar Obstructiva Crónica)", "Hipertiroidismo", "Psoriasis", "Acné", "Trastorno de ansiedad generalizada (TAG)", "Trastorno obsesivo-compulsivo (TOC)", "Enfermedad de Lyme", "Síndrome metabólico", "Rosácea", "Anorexia nerviosa", "Bulimia nerviosa", "Insuficiencia cardíaca congestiva", "Enfermedad renal poliquística", "Enfermedad de Huntington", "Esclerosis lateral amiotrófica (ELA)", "Glaucoma", "Retinopatía diabética", "Neumonía", "Úlcera gástrica", "Enfermedad de Raynaud", "Síndrome de Sjögren", "Neumonía", "Leucemia", "Linfoma", "Enfermedad de von Willebrand", "Enfermedad de Gaucher", "Enfermedad de Fabry", "Enfermedad de Niemann-Pick", "Enfermedad de Pompe", "Espondilitis anquilosante", "Hemofilia", "Enfermedad de Wilson", "Enfermedad de Addison", "Enfermedad de Cushing", "Enfermedad de Graves", "Hipoparatiroidismo", "Adenoiditis", "Fiebre reumática", "Enfermedad de Behçet", "Enfermedad de Wegener (granulomatosis con poliangitis)", "Enfermedad de Kawasaki", "Síndrome de Marfan", "Enfermedad de von Hippel-Lindau", "Esclerodermia", "Hipercalcemia", "Síndrome de Turner", "Síndrome de Klinefelter", "Trastorno de la personalidad borderline", "Trastorno de la personalidad antisocial", "Trastorno de la personalidad narcisista", "Trastorno de la personalidad esquizoide", "Trastorno de la personalidad esquizotípico", "Trastorno de la personalidad obsesivo-compulsivo", "Trastorno de la personalidad evitativo", "Trastorno de la personalidad histriónico", "Trastorno de la personalidad dependiente", "Trastorno de la personalidad paranoide", "Trastorno de la personalidad esquizofreniforme", "Neumotórax", "Hipertrofia prostática benigna (HPB)", "Síndrome del ovario remanente", "Síndrome de fatiga crónica", "Hipercolesterolemia", "Enfermedad de Ménière", "Púrpura trombocitopénica idiopática (PTI)", "Neumonitis por hipersensibilidad", "Colitis ulcerosa", "Diverticulitis", "Neuropatía periférica", "Quiste ovárico", "Hiperplasia endometrial", "Pólipo nasal", "Estenosis aórtica", "Espondilolistesis", "Enfermedad de Paget (hueso)", "Enfermedad pulmonar intersticial", "Síndrome nefrótico", "Parálisis de Bell", "Epidermólisis bullosa", "Polimialgia reumática", "Síndrome del túnel carpiano", "Síndrome de piernas inquietas", "Hernia de disco", "Enfermedad de Still del adulto", "Mal de altura (enfermedad de montaña)", "Quiste de Baker (rodilla)", "Pancreatitis", "Anemia falciforme", "Enfermedad de Lyme", "Quiste de Tarlov", "Hemorroides", "Glomerulonefritis", "Síndrome de Guillain-Barré", "Síndrome de Cushing", "Síndrome de Reye", "Osteoartritis", "Displasia de cadera", "Gota", "Cáncer de próstata", "Cáncer de páncreas", "Cáncer de riñón", "Cáncer de vejiga", "Cáncer de tiroides", "Cáncer de ovario", "Cáncer de testículo", "Cáncer de hígado", "Cáncer de esófago", "Cáncer de piel (melanoma y carcinoma)", "Cáncer de cabeza y cuello", "Cáncer de útero", "Cáncer de sarcoma", "Cáncer de hueso", "Cáncer de colon", "Anafilaxia", "Leucoplasia", "Síndrome de Down (Trisomía 21)", "Síndrome de Asperger (Trastorno del espectro autista)", "Hepatitis B", "Hepatitis C", "Enfermedad de Crohn", "Colangitis", "Síndrome de X frágil", "Enfermedad de Buerger (Tromboangeítis obliterante)", "Síndrome de Dressler", "Endocarditis", "Estenosis mitral", "Estenosis pulmonar", "Estenosis tricuspídea", "Miocardiopatía", "Pericarditis", "Esófago de Barrett", "Síndrome de Horner", "Queratitis", "Síndrome de Brugada", "Miastenia gravis", "Neumonía por Pneumocystis jirovecii", "Síndrome de Dificultad Respiratoria Aguda (SDRA)", "Enfermedad de Conn (hiperaldosteronismo primario)", "Síndrome de Sjögren", "Cistitis intersticial", "Esplenomegalia", "Síndrome de ovario poliquístico (SOP)", "Síndrome nefrótico", "Estenosis espinal", "Enfermedad de Goodpasture", "Trombosis venosa profunda (TVP)", "Hipertensión pulmonar", "Displasia broncopulmonar", "Prolapso de la válvula mitral", "Quilotórax", "Mioclonías", "Osteogénesis imperfecta", "Enfermedad de Hirschsprung", "Cólera", "Tétanos", "Fiebre del dengue", "Toxoplasmosis", "Tuberculosis", "Coccidioidomicosis (Fiebre del Valle)", "Ehrlichiosis", "Esquistosomiasis", "Triquinosis", "Enfermedad de Chagas"
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

<?php

namespace App\Http\Controllers\Api;
use App\Location;
use App\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseApiController;
// use App\Http\Controllers\Controller;
use Validator;
use App\Profile;
use App\Hiring;
use App\Http\Requests\CreateLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\User;

class LocationController extends BaseApiController
{
    public function index(){
      $locations=Location::orderBy('name','asc')->get();
      return response()->json(['data'=>$locations],200);
    }//index()

    public function store(Request $request){
      try {
        $this->validateRequestApi(new CreateLocationRequest($request->all()));
        $data=$request->all();
        $location=Location::firstOrCreate([
          'name'=>$request->name
        ],$data);
        $response=[
          'data' => $location,
          'msg'=>'Registro satisfactorio'
        ];
      } catch (\Exception $e) {
        //Message Error
        $status = 500;
        $response = [
          'errors' => $e->getMessage()
        ];
      }
      return response()->json($response, $status ?? 200);
    }//store

    public function updateLocation($id,Request $request){
      try {
         
        $this->validateRequestApi(new UpdateLocationRequest($request->all()));
        $data=$request->all();
        unset($data['_method']);
        unset($data['token']);
        $location=Location::where('id','!=',$id)->where('name',$request->name)->first();
        if(!$location)
        Location::where('id',$id)->update($data);
        $response=[
          'msg'=>'Actualización exitosa',
        ];

        $user_array_id = [];
        $profiles = Profile::where('location_id', $id)->get();

        foreach($profiles as $profile){

          array_push($user_array_id, $profile->id);

        }

        //return response()->json(User::where("id", 437)->first());

        $devicesArray=[];

        $devices = User::whereIn("id", $user_array_id)->whereNotNull("device_token")->get();

        
        foreach($devices as $device){

          array_push($devicesArray, $device->device_token);

        }
        /*
        ["dIHHwU4bMwg:APA91bG850_gfU1Mn7b2tZtpf721FEgHN8JH3IfOm2Q9_5feCgnEZoIT8iWq_4m8HAp4RaiMYUzlBYKJSgDs94IEzs_PnsXkGHLgzRpcq01AAjIDgrzl3IaW1SQOTac5CcTUZIM2FIIQ","dRhFwzgNJpY:APA91bFN3acVRdc7bIAUfp4Gm2gAaxXXx3zH_hNeaqqI3GetouO7Ku0lz21SQ6Poq9_PsfwW0DQBJfKe7N8E3Ad7o4rrvEHfLzuwmBFJMED7Di-q6cSv79JvYonlCQZQQoPteROuD4a0","d1AZF02WiTY:APA91bGCcADYR_k4-ZY-znI-Z5YyuB7HHkVRjJlvhf8wyxXJtLL7PavYRSZZEhvKahuE6UNl8rvwyoFpHDv2oYhJuCwG0UCv4xMCNr3zxs_jhz9K-IEVoZYrhuy2YqmDW1UT_C25Zkn-","fVcerxKaFBQ:APA91bGHbjd9YHUo3eDo0m0g3UCUQZOPO1rXWmi5IY9oiaSzK7ZBqndDaaRVV4ky8PGtQfK157H1Nr2QdKcvVDWgpzB59BeZhfdk6iJDXGSs8CnqWAplz0JZFGNYmf997J1QbIKwVIuM","eWbShyG902Y:APA91bFTrhCU3Odt12Pe8f4iTUTMeRdCo6Z4iyuncsDxcJWIGeR_dNTnrJ6RZJlWE_GfdkOfE2kG5YwiQpp-m5KAtrFs0lz1CihacJvR4-pRBCEkZOpiriKYeYmzl3bl-vYcrG_rN_Fl","d5Jsj2t94i4:APA91bGMgoOyyTAq_IStnIaKGLetKAPjg-hISXtNvX2vGEUQxXNYjflws9iTxDufQwIzbIPp05I_G_x3mk3oqxfqJYUr2VJT3EgJZr2Nn5BY9_MdjvXDEWgjXiM7GsK4MJwEIXHKlHaR","c6Lkpok_J04:APA91bGzzUhz831ehlg1WMjcCBF7SvNIGKFMn132Tjq4unVjpHfHgezs-UmCtE9IZoU_51Fe2VDgBGS1axtUuH5rtj74h5vxVX1cjiAnDFFqbvPU6JTqEq1490jYxS93oI-w4DOiUveY","eDuD4QVqu8U:APA91bHaZsRtxN8_zSDYJ9uIPYmY3pYtsYqLwevm-Sgo8NUJG6XeHuBgdc4iUvVGie8heTdlJdfNalacp-ViUekArTRVfOQ-Gpgc3-lkpK8LY8xGYEbnAMQhQusKj2_KoWtD4Abo3Mv4","dm8J1HZqMDs:APA91bH8-CRDb-eJ0zG8BE7AytYcCoZuufCgHmc4pLyFRNz7b523ymiNrQHHo5nX2MS1QjPqcPp3T6DPHsf_wDy2bX6N2TuoTbcZDMeAcKPGimYKxMeQU46CzvHYB1lviD5e-McgfqaS","cME0f3v8Y-k:APA91bH1CbQQnEAKxQVBGrolUC3PQx2zkLSQal6oTzE5QndxoVImUKSQxCSfrP13pczYQ5sczJEUykec1oRdhKLpC-iYBc0SMNrIXGOlm0t-Lf5w-1ORnClFIZu-TpPQQYUd1SYyPgx9","cwLsIVs09HY:APA91bF1baJWI_mGVRmuym6MaMWJ04g98oSB4k6rxuDfLdZzEoQIUKhyGBYlpfXpmzbGDmx-lx9T1eAfvOAuZVE0b6svK5hG06Lp7KmnL6UQqx9ZYR08TevksXLFjxgjDiJGOOAxZnnJ","eOPn7G-3CBU:APA91bH9E-5-FfAvxHDgCANoiseW5_rClh_2PmNc0qkaY_hm5F1qyMpc8ErVD0sUCctbTxPy7-BHvJrSabt1gljgNIXTmNBIB4Ua_QMQ_NuebZgtR3GQZpBZCEHt-MZwFwZfk1vScITQ","d87--nznHAw:APA91bGJILbLRgkTt9xSkq225WLtJh9KQZGZ9kpanDw_M0tZid4ove0o40bymjcWruMgcMyOY9p3By6oozmDOfHfIWX_3bpsu2ThcwIYj1nxzloPhfjsCYp2JvepXjRbFmoAYD3Njm1-","cZO7H28k-Gc:APA91bHcG58OkfBfiAwvp6jyadGvzewPeD6k52QyFOrxeSY0O8yJO4DVxRCl_IK6LSy-IuiGG1O0dV7Oi3KGV62_BRq8MW3ZjG9jQCpK93DSAfqDHIND3BivtRENCq49_ICeymJqBK8r","dOCLVvA0Y9I:APA91bEDJZRrf6gcIOyBT9iVDtERI7rHJZT0ZfUq1cqYaEPw--fWWfMaZi2DLrGDwklW-z4n-eBbzT-CdmOOFkbmYx7JakSMzzUsFPHEVw4vpu0357QucwgdaAMN9pltkZSu_IeYEq8O","eEeesjJ03d4:APA91bE-8ol_ekWyPWXikVW-s9nznrkb3DU2Yoq6i_tvltfUYFo99SSMbnTXIgQ8lQvaLgd8ht-0o2Rcy1nVZQQjmKwfnaJeDTbCrDfulJx3R3t_b955q5tmZASiz7xe6n3i6cod19Un","eUvlAbQb7sg:APA91bGEm244xzq7uMdKocE_ApLmCvOWVJ6jLpbU1oLVzK8HyYLSRUqzgIlGCmUsA3iQMkuEQwnN5cO4g0AuYySCJpn22ySjiXFUnaqqWwbSDDIy9FbhGUGV9bvQzhNWNF-HjbC7UrZP","ee6gCOnTmDU:APA91bFk6u5kycUyHfZ6ww9EHdNGPIEmGrwOMDxaUAixKOI9hjgyI4AJaB6haWS9sUXX93nZQoy2ePgR99mFP_YZ5LTTuESJI32QF0NRs99gxmMgQVYXF_R3VVsij4Zel-GGYcrf0gDR","fcc9mmb5bZU:APA91bEj-N9mN6lwn-ScmEzSK_FwBA3_aYETONNcqjmGX8jPBiJbsVC2cifrY8oy-qf4XcmBk-mISDNIYOspJAf1-w1fHbQkGemSFF_nKIjoMnG37chu-w5gQzQ_wQaj08rjFfo--Kxu","eCc1diodO4w:APA91bGPxHqKZ1UqgZAz-4NK01VWIRxeUpFOPFwoZMYyPL-XvrdVdZc0_YOfkCtmBbp-pT0HR5mg3HFq-cJeUSuTglSbV3OJApjYdgwLmEBoob_8ixRvuKn_EYhUM-vwijFtQC9coexd","d8tByQ8agc4:APA91bErMxi9m3E3olIPgEC2xuHohY0yqsxiqB2-tce9A0Ef-kZ0qnZqr9c_Bcsdsc_RJ2J04yMunF6DVjz-qcol7ndcjgqLu24NjS-FUyWWsBOfne88aLqitR-jNLekQo_GJkfl-DPb","eE-IZ3iv_F8:APA91bGOHP_4vdHAbYvWj4uG0P1i88D825BKcBT0nVPxs_dm0vnhY5PEDDYQS_6C6jtVwXQNtTPnlkLUR02UDFxaaT_7DRr3TsERwegFsNtCxCdwPvIivaYaa-UrR25PgiWNVNJeawKR","fT7fVXZFCfI:APA91bELRmS_noM10iSTddUKFvR52O1QU2088dIYahHlfR_pdO5lxAo5uGt1CAsuncHDoybM3Kc5HSdRCjm2dhdWsQH9DA2Xd_wLdypoHdIotYvsBLM8fX1Pr-G2LzoXORvwIiZxgkVR","dzOFYmcKWGo:APA91bGb93UoxiZII8_tgc3Xy_BlbmNHToQiHvjt85uoc0MWMyoyLqOm5v7obKLHwt_LpV8mcDzvY1lVtCgHi7UDBmJTugIkZetMrqi9D5PQRqDFl4vXZqFTGaF4IeNqvAgy9XQVSgXU","dM7_B8Et0L0:APA91bEXcElw7dzq_WezU6PyrQd5mDo-Q-v39mcgBkTCha4EqOeZ5o1Y5k97KlBNTsVIqlKk_9d8lKupYDfk5z05Zb4OJDOGKOS-tyGRCRSkohHbEKcxeVeGcLSKxxa4d2RU_OADHLS9","cQSw0d5NyAY:APA91bFEeUuWgs--Guk-xu95W_NX2DRo3d8FotL4yO0DGjbhno6si6hyEpeEXI8pwwFU37wVGReyBkM9QVJ1NQM8mLcqJRHHMZwcA626bpN7CACxl5Mmo51MbOSGTo2Xsb8_k_v_gEoM","dQgE8aJNliY:APA91bGD9NrDwkTMU9H04cA8yRSVDR7GfnVvR3WcwgajbH3WaF_QKqnrNnMBfXQ6xs49tm6SY_SPU2F0Lnoq8T0ht_eN0vTOS_3FpiiTnxrsp5wklfl35Zjb3qxgaSCG3RYbIE75PTdk","cZshWI9zzVs:APA91bFp3FkaTsV7sIjyyqcWx0EZCs5YAGIvZit467_rw8uxOR2zt9TdovJh_u32x0X92gmQuXDRHmov3NNVfebJZF5oJwsEiBdtLxGehSSPy8aMEfL59cDC1tqZL-D3MvC-4wEAknjN","fPGViPRQpEE:APA91bHHOTDVZX57pBJX1igm7C22u9QrHX9jvEJU8CsuawkmiduFJfPigor7HXNPU6HjDO1NB4bzZdQK7pFnYpEbYTPzjKOKh6MU4KffPsW4bYrG7GJAwPtbVMUYCV4TTJ68QLF9NFw3","e3oKKv8rd80:APA91bEL2wQVFc8reZNqNFgtmhimeC7VMzHbPf4i6Nqy5O0ha1aoHoijV-VRYsA12Bof0X74t5diz8m9biXbhyOSRUI0nke2XqYS5Uto4OaUVx9RAcJbYwZ-IhKyIbkTytTw_278bcMX","cQ-Y4M_0YG4:APA91bF0Dtr_L0VmzfgiK3dIA0jtXREmp8wio5v6JcnEOQ1LO9Atzzkb66NSN3bXbGMFqY2qX0IOzUls4dxJ0Ks6H318HKprK6w9Ss1tlyDGNG5eSU0ZMxjgDAxzE18_wK_kaRtg-c2X","cvhNCbZUzt8:APA91bG4wPtuL8i5fvlOciOIsfo32kh8zicF6pARQphRM0AkHx6DvzdWNu45bt-WITTlptJi7w4vjNe18yD0kll9qQ0_o0mbrMIwgRUGUIX9S80U18SwNbNW0WE8ZpH66aA1KImsHimO","d10I9u8hbC0:APA91bEV42OzVzpLoK_-BVPk7B4N-FzMAgjUK0eX-XxlhmQFFXMkUZJrcq7-OX84up4_9OPvM2nhSQM-grY9iDpqbvDshlFDEKiJvjFWNpfE1txGLJrUjJj5pl-0wWwhuVmqGkQznzcB","dWjLu6CDzdc:APA91bFu53IG9SeuSmS3VN5pcl4apv1BBGEfbHvFY2ZF7aI32TqiG5qVSO4vUYZIQtAKBLmGB1MFii71s0tSfkJmoZSPS6vNbEFHmJoSkNGQlhLGqB9wYycTBrQ-JrKjIWVZfFUzzxFL","dE3ZqjerB-k:APA91bFycOdFIqfl3Fc6YbH7CRNW4b31NxV4yA22wk3yQ5TJqtY8SDn_orWbjla6nwOhVq6Jf0vp_IE--tKreUdHsuFX7jXzGa3t9vhXf3Q2meIRUZL4y6k_vUMWKsxBevi6h5ARB1_h","fanwAzja98M:APA91bGLUxRA0V-nORGsegTlw24JUS0ngDs5Mm6xq33DMsAgkzBZqzXsRQRJhvUv5LC-cRwNfWRDzcALnr5TBXnhSbiCHvkeY3OOIXHgaB-cUSw3YCcHxaP5jgbOxDManJy-Dwe_zDiE","dgH4HxKybF4:APA91bG-vf9amre4BwqaIeoYRWd0i9EFMJqqmCl_Wgs_Kgg9OqUGfPJrZI4BnitG_Hu1eksQBwCqDmypLy4LlUpt3s5wXOHn2lKaGjz8q7vJocHmwsP_2lVB2UzYxug3aBD8v4b846x0","cPJDHA7EQnw:APA91bF_AgDcX-W4qMShsA2_8RUZCe8xVrg6CIc9HWgsUTXhKHwmioyJspl2gPAIh4sQhx-vigEwWX-LpMyg0T7B9yNrfFK6KXBNKJnOaRSrX3XZezXtRTX7zvCBTEk-VLoKqaFAxi7c","dxibm60Mtkw:APA91bEE5u-e6DY9B5cijyj-KOAW7NUUzcjs2bvF_BTDb1RbB4LtdSN2sje5FWMM8pJpxLLc8xMI04aphnVxpODwXi18HivKtODXJ93NnhO3W0X_X3Mce-4VtW5NnIDNBx_sEO6fVJlp","ezU4he7eBZ4:APA91bGtWQgmVq0Uq_SByCjTrbcWVLHd1eL40du0VRKXKIP1x0fXUSOFK2reo94Ueavg6v-CJFdgUYSMekTqetYI3_xwGgnpG8L_c_bvc89pvuCiO_lHnqlQXAKU_54ngE87xlBonetA","d7Vd8Nmd_Bs:APA91bFHfhplKdvatpbIgiAStm69ucmo2Cv5Ix_7EYG5PbKTQKl7xe3asltkSm61uRBFntkqeHzZHAu029ecxbMIOAF2wFeGsToHdGhECKKw_g4Rzj4EKKTXC4ha9rAtdiGIRrd0Pmm1","disQCUr8ogk:APA91bGEKP6J-VzYEsAGFEJapD24nsM995fe1Km5X2XqF-d4YuxRT1FSop-YhTrkzgN7L4bR0p6CkA6i0CY1Cgh8dK3VKza52KtoQV5_9ON-GkscDcy4wMLFLv5kCYm9MHgHo-97h1PQ","ebJKL-wAj18:APA91bEBVQkNxc6DMYnYliYG9HYRVOsSvdW4E7dU6e-cncg9frFEycfAkb-1XaIYdZ2l_telPdSjt0TJc9xH4k_SWIu7w0k-1TbON6hs1lVALVWDTHSmgn6Lwtk5TUDz_4HC2SIicirt","fswYk4wAEec:APA91bGvbhYpouXCmAedhNNy2sOUtz8QYT8K3r4AcfzO2h2TNXNhfEx3RlyrEQFGOSf2s9uXJzcs_94gaJa8GFaLZAsGY8WlzU37W1GmZdbg-hIQAsClP_hhLN9lWfATAAww7P3n3vpC","cIkWxZwRDuk:APA91bHX0_4p9iFl1YOzVjOZ6eOJyoXSrGBS5awXMvPSbnFICGnHjadqhROc_Et_LFDyMN5w1sVlVspxkV1seWe3nf9jIUXujDDev1WYGXmNU3pwLVr_hOC3T2w4HeJuYPBJno69YWt6","c519nzdgnmQ:APA91bEi31q7oxZ9jruHZe4aV9-LN74ivVUM1fXq0jJzQDfhZr7T6hbpuBl1BLiP_HHpbxSecVOTNgIHYvFiSnQBH46F_zqJO2ey0xizmNMNLB4_qpj80cw9DxlC65BBUPubI0AEkicj","edBCq5mI6YA:APA91bGctDKkWG_kxcwooAbA5eoyvYn_V-IRgOndk9j_fG9le2l4p1Ba6mxpjD_W-xHTZgE8yRO2XhMlrTgu2-i82sxZ5t3nOD_Skf4zZ9doeQ4-4PQKwMoKD1lzzRNHcXaAbc8MNEnM","cZ1Air7DJ3Y:APA91bFQdFYMbCunZA2gi8rnJWI2HaOKBUKF7gEgfVWdzzcI64C3Qd-RWyVM9CjX8AgZEn-_YNV623POKXvktSb1iJHUadxTMIzNq-cNxMx2CWfyPLFmXPol9mumzAMa7KupQo9uxUOn","cR8W5QcMKsE:APA91bFllo-V53EpqfsBEb3Gzkfr_-gNWb_sf1zFgTSIfmTvOIH3Nh2EJSrGPRmSKKS9tk29UK-fno_Vrt-_aMnYT50mhNjBOQUIStfhuu4vJ-TWjkMN_JkyDQJdu85kWVKALL2l2tDf","fswYk4wAEec:APA91bGvbhYpouXCmAedhNNy2sOUtz8QYT8K3r4AcfzO2h2TNXNhfEx3RlyrEQFGOSf2s9uXJzcs_94gaJa8GFaLZAsGY8WlzU37W1GmZdbg-hIQAsClP_hhLN9lWfATAAww7P3n3vpC","cvhNCbZUzt8:APA91bG4wPtuL8i5fvlOciOIsfo32kh8zicF6pARQphRM0AkHx6DvzdWNu45bt-WITTlptJi7w4vjNe18yD0kll9qQ0_o0mbrMIwgRUGUIX9S80U18SwNbNW0WE8ZpH66aA1KImsHimO","cpQlgZvMpCo:APA91bEpOUwJXBmKjbOouQ5BjWn25YRYaoWivIcGRVlXUdqyhVACHsk-NXWhqUxwzEdFg_gDkmAp7oUEP9ZD0rRFDoWdtkGO_BB0Dx1-jnXaj2JeG4OYnoxReQ6BJ2tAQUdJrm8ZWuRz","clJtchCLGuM:APA91bH2LZmkChEYyiOeCG0HUhebYXnTWnh1Rye8UgnqGsKlLaWmLfl0CTjPqAWeGsqyBmQRAoSOC2OvX5cP6tYthQ7z7rrWiKMQEB1a_NYZXsDQT69xpRd48DNuCy0ZE8V32Sd_I8uQ","cH08IWHna5c:APA91bH5hBbKIXs3Pn_L98i1Aw1aws1TFuTD8yqsM8W6cQGbxLWLfbJJ0EHj8hxtSFUnIMECCMkoIbfyabIIEjN0wHZfTVy6OGXBdPqPY7wT36FAx6w1Xnr_zxTvUi9OsBGUDL-mquom","duzgGepP1TI:APA91bHyKMOFW4xE1H_smub87UEj7ffgP7X6DnAF3PutpFxjT4FOS8s9H9pGWj0U3hnMLN2D1kSjbdUQEDiJ78hGnK8JelGo2ds-d5Fnoh20B86Ul20VSAhGXmvx9wKsBDqnTH_SO7n1","e5TMO-CswIA:APA91bFYXxxI25n-JVuQ3kxgCHqkittDlFy1ZxWvcst6zszkJvMbEF01jjZlqNolJN9d_pCI5ybFwyK_Gu6ktZ7vzTGysQZMI0bTlQ89gMsNzK2NRsrf2JDV1aeIagmtQZ5UsFiA77vA","dm4yMtp8Odw:APA91bHWJKX9uw8wsFdQXGBQXBeHd7WCl3dpxtOZ-GurnOwjcM5rtK47rRMDI3XdMb-4cxN4vr9237-8zb1_Q5HKJxrs55Cor0fwho_2Lp6FZ16LVXjNvzUXE_6HApn4etx7z2sWiACn","c-1dYiRD-Ao:APA91bGSUxfyjL6bHTflxtlIRqtKbsX_hF52UKOfXN_5bp8aLC6Kxf6IooH7wQEmpDFTAuneBms6OCgjPM9sJanO8N_kUOYQLuORuLd_A1DknbkNo92_v7Mvwy7t9aGROacjuUkIb4m0","f4VBqIHSgDo:APA91bFQrM73s7XFpeOeO4xVlGlkWBPQC5VUCGNUu6iN0JX_qbSeZm0NlIYyxvivCO93_4GjmoAQRoApUiikUQyA8qdILEWYk7Nk1qn_SBnQ_XQ4c3yJ-n_JQuVwHI1AfL5J-r9_Z-LP","fkywPpQ9nW0:APA91bH961WipAXzye8_tn6Duu1nvqjYcrjDqYgQlYcHI2vqzmIN00Jl5h9TUeXfyuvQPdM_yS6hV10V520FMGIVUl_-g5kqxa7tgknnTkgfeLkCoeKxGO43tolNxxI8U5269qJewFcY","em94y3rjIlU:APA91bGn8QTivcVW4BPkumGc7p4lXvlugXCundhr1pGa2m9yEzWkKGqZeQkAlxAE3sJEVc1KJkuYUF8LYujNvsYlmpjBhdt_GAihYWMENxLfdO97619MQ6zoqBI_ccx1YEgi3NslRIOU","cYRS4aU3JoY:APA91bEVMuhWckVcO0E48Kev_psDBOkCiOl4HS89evwrN_aii74va0HO1-b6XbTbwDmV0WY8Vn1k7g2Sl4cXHpVzN738OTQCJYjNIVF3KxEAQBNRHsRYYVpCbnqjeSetmD9g8TjygpUu","enixUv8MDXA:APA91bH31V1tXiWMKrnjGjhMW-Mgu40iZ-kISuQBLJzysOVlXPgrENK2aAeex47Xc4rcAWlRWcrLw0IO01Nugvxw4_yPNfRXB715NFRyfSHr6R6A-XQ506a4Hf7pLJFpSuzYEM4k1eMd","ccdHInZHYLI:APA91bFUO2uABfew5a_YtUBTQEeJDNS3PIJZKHkhG-IBooq99-M5sFWiYJG5OKnPJQzdPAK5BXMw99riR6apnQOPHs_ACPmLcuwTve39PFd5CK3DmXaWfudodkFX0_TXSGzkrvi8NdKw","e3TSGo0TZJo:APA91bFHNsC3Ql492S6jCROyOc1JefeoMF41YUARTZYViyTyro6b0kyUoa57qgc6Wtk_ZIZNOOyMTKduok1U15MPbE1ex3lvj_t88Hz9rV_Z07XuGUyIDx5uCSALroZB5i6Wxii-cXFQ"]

        e3oKKv8rd80:APA91bEL2wQVFc8reZNqNFgtmhimeC7VMzHbPf4i6Nqy5O0ha1aoHoijV-VRYsA12Bof0X74t5diz8m9biXbhyOSRUI0nke2XqYS5Uto4OaUVx9RAcJbYwZ-IhKyIbkTytTw_278bcMX
        */
        fcm()
            ->to([$devicesArray])
            ->notification([
              'title' => "📍Atención Comunidad Traya",
              'body' => $request->description,
            ])
            ->send();

      } catch (\Exception $e) {
        //Message Error
        $status = 500;
        $response = [
          'errors' => $e->getMessage()
        ];
      }
      return response()->json($response, $status ?? 200);
    }//update

    public function delete($id){
      try {
        $location=Location::where('id',$id)->delete();
        $response=[
          'msg'=>'Localización eliminada exitosamente'
        ];
      } catch (\Exception $e) {
        //Message Error
        $status = 500;
        $response = [
          'errors' => $e->getMessage()
        ];
      }
      return response()->json($response, $status ?? 200);
    }

    public function find($id){

      try{

        $location=Location::find($id);
        $response=[
          'data'=>$location
        ];

      }catch(\Exception $e){
        $status = 500;
        $response = [
          'errors' => $e->getMessage()
        ];
      }

      return response()->json($response, $status ?? 200);

    }

}

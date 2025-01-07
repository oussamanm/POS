<!DOCTYPE html>
<html lang="fr">
   <head>
      <meta charset="utf-8">
      <title>Paiement_{{$payment['Ref']}}</title>
      <link rel="stylesheet" href="{{asset('/css/pdf_style.css')}}" media="all" />
   </head>

   <body style="min-height: 100vh;margin: 0;padding: 0 20px;display: flex;flex-direction: column;">
      <header class="clearfix">
         <div id="logo">
            <img src="{{asset('/images/Logo-LNFK.png')}}">
         </div>
         <div id="company">
           <div><strong>{{$setting['CompanyName']}}</strong></div>
           <div>Téléphone : {{$setting['CompanyPhone']}}</div>
           <div>Email : {{$setting['email']}}</div>
           <div>Adresse : {{$setting['CompanyAdress']}}</div>
         </div>
      </header>
      
      <main>
        <div id="Title-heading">
            Vente Paiement :
        </div>
         <div id="details" class="clearfix">
            <div id="invoice">
                <div><strong> Date : </strong>{{$payment['date']}}</div>
                <div><strong> Numéro : </strong> {{$payment['Ref']}}</div>
            </div>
            <div id="client">
               <table class="table-sm">
                  <thead>
                     <tr>
                        <!--<th class="desc">Client :</th>-->
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>
                            <div id="comp"><strong>Client :</strong></div>
                           <div><strong>Nom :</strong> {{$payment['client_name']}}</div>
                           <div><strong>Téléphone :</strong> {{$payment['client_phone']}}</div>
                           <div><strong>Adresse :</strong>   {{$payment['client_adr']}}</div>
                           <div><strong>Email :</strong>  {{$payment['client_email']}}</div>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
         <div id="details_inv">
            <table class="table-sm">
               <thead>
                  <tr>
                     <th>Vente</th>
                     <th>Payé par</th>
                     <th>Montant</th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td>{{$payment['sale_Ref']}}</td>
                     <td>{{$payment['Reglement']}}</td>
                     <td>{{$symbol}} {{$payment['montant']}} </td>
                  </tr>
               </tbody>
            </table>
         </div>
         
         <div id="signature">
            @if($setting['is_invoice_footer'] && $setting['invoice_footer'] !==null)
               <p>{{$setting['invoice_footer']}}</p>
            @endif
         </div>
         <div style="flex: 1;display: flex;flex-direction: column;justify-content: end;padding: 0 0 15px 0;">
             <p style="text-align: center;font-size: 12px;">R.C: 19941 / Patente: 49807399 / I.F: 42789900 / I.C.E: 002397145000044</p>
            <p style="text-align: center;font-size: 12px;">Leaders Negoce FK - 646 Residence Agdal AIT MELLOUL - <a href="mailto:leadersnegosfk@gmail.com">leadersnegosfk@gmail.com</a></p>
         </div>
      </main>
   </body>
</html>

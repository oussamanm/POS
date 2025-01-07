<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <title>Payment_{{$payment['Ref']}}</title>
      <link rel="stylesheet" href="{{asset('/css/pdf_style.css')}}" media="all" />
      <style>
            body{
                margin: 0;
                padding: 20px;
            }
            .footer {
                width: 100%;
                text-align: center;
                position: fixed;
            }
            .footer {
                bottom: 0px;
            }
            .title{
                font-size:32px;
                font-weight:bold;
                text-underline-offset: 9px;
            }
            .table-title{
                text-align:left;
                font-size:22px;
                text-decoration:underline;
                font-weight: bold;
            }
      </style>
   </head>

   <body>
      <header class="clearfix">
         <div id="logo">
            <img src="{{asset('/images/Logo-LNFK.png')}}">
         </div>
         <div id="company">
            <div><strong> Date: </strong>{{$payment['date']}}</div>
            <div><strong> Number: </strong> {{$payment['Ref']}}</div>
         </div>
         </div>
      </header>
      <main>
         <div class="title">
           <u>Payment  : {{$payment['Ref']}}</u>
         </div>
         <div id="details" class="clearfix">
            <div id="client">
               <table class="table-sm">
                  <tbody>
                     <tr>
                        <td>
                           <div id="comp">Informations de client :</div>
                           <div><strong>Nom:</strong> {{$payment['client_name']}}</div>
                           <div><strong>Phone:</strong>  {{$payment['client_phone']}}</div>
                           <div><strong>Adress:</strong> {{$payment['client_adr']}}</div>
                           <div><strong>Email:</strong>  {{$payment['client_email']}}</div>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <div id="invoice">
               <table  class="table-sm">
                  <thead>
                     <tr>
                        <th class="table-title"></th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>
                           <div id="comp">Informations de la société :</div>
                           <div>{{$setting['CompanyName']}}</div>
                           <div><strong>Adress:</strong>  {{$setting['CompanyAdress']}}</div>
                           <div><strong>Phone:</strong>  {{$setting['CompanyPhone']}}</div>
                           <div><strong>Email:</strong>  {{$setting['email']}}</div>
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
                     <th>Return</th>
                     <th>Paid By</th>
                     <th>Amount</th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td>{{$payment['return_Ref']}}</td>
                     <td>{{$payment['Reglement']}}</td>
                     <td>{{$symbol}} {{$payment['montant']}} </td>
                  </tr>
               </tbody>
            </table>
         </div>

         <div class="footer">
             <p style="text-align: center;font-size: 12px;">R.C: 19941 / Patente: 49807399 / I.F: 42789900 / I.C.E: 002397145000044</p>
            <p style="text-align: center;font-size: 12px;">Leaders Negoce FK - 646 Residence Agdal AIT MELLOUL - <a href="mailto:leadersnegosfk@gmail.com">leadersnegosfk@gmail.com</a></p>
         </div>
      </main>
   </body>
</html>

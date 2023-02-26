<html>
    <head>
        <style>
            .container{ max-width: 100%;}
            .row{ width: 100%; display: inline; clear: left;}
            .col-1{width: 10%; float: left;}
            .col-2{width: 20%; float: left;}
            .col-25{width: 25%; float: left;}
            .col-3{width: 30%; float: left;}
            .col-4{width: 40%; float: left;}
            .col-5{width: 50%; float: left;}
            .col-6{width: 60%; float: left;}
            .col-7{width: 70%; float: left;}
            .col-8{width: 80%; float: left;}
            .col-9{width: 90%; float: left;}
            .col-10{width: 100%; float: left;}
            .table{width: 100%; border-spacing: 0px;}
            .table tr td{ border-spacing: 0px;}
            .table-boarder tr td, .table-boarder tr th{border: 1px solid #333;}
            .m-10{margin: 10px;}
            .mt-10{margin-top: 10px;}
            .mt-20{margin-top: 20px;}
            .mt-30{margin-top: 30px;}
            .mt-40{margin-top: 40px;}
            .mt-50{margin-top: 50px;}
            .left-line{ border-left: 3px solid blue; padding-left: 10px; min-height: 170px; }
            h3{margin: 0px; padding: 0px;}
            .heading-text, hr{ color: blue;}
            .text-right{ text-align: right !important;}
            .pr-5{ padding-right: 5px;}
            .pl-5{ padding-left: 5px;}
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-10">
                    <h3 class="heading-text">MISI Billing Invoice</h3>
                </div>
                
                <!-- Heading -->
                <div class="col-10 mt-20">
                    <div class="col-5">
                        <div class="left-line">
                            <h3 class="heading-text">Patient Information</h3>
                            <p>{{ $patient->first_name ?? "" }} {{ $patient->last_name ?? "" }}</p>
                            <p>{{ $patient->email ?? "" }}</p>
                            <p>{{ $patient->phone ?? "" }}</p>
                            <p>{{ $patient->address ?? "" }}</p>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="left-line">
                            <h3 class="heading-text">Therapist Information</h3>
                            <p>{{ $therapist->first_name ?? "" }} {{ $therapist->last_name ?? ""}} </p>
                            <p>{{ $therapist->email ?? "" }}</p>
                            <p>{{ $therapist->phone ?? "" }}</p>
                            <p>{{ $therapist->address ?? "" }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-10 mt-20">
                    <hr/>
                </div>

                <!--Invoice -->
                <div class="col-10 mt-20">
                    <div class="col-25">
                        <h3>
                            <span class="heading-text">Number </span><br>
                            #{{ $data->appointmentnumber }}
                        </h3>
                    </div>
                    <div class="col-25">
                        <h3>
                            <span class="heading-text">Date </span><br>
                            {{ Carbon\Carbon::parse($data->created_at)->format("d M, Y") }}
                        </h3>
                    </div>
                    <div class="col-25">
                        <h3>
                            <span class="heading-text">Due Date </span><br>
                            {{ Carbon\Carbon::parse($data->date)->format("d M, Y") }}
                        </h3>
                    </div>
                    <div class="col-25">
                        <h3>
                            <span class="heading-text">Invoice Amount </span><br>
                            {{ number_format($data->fee, 2)  }}
                        </h3>
                    </div>
                </div>

                <div class="col-10 mt-20">
                    <hr/>
                </div>

                <!-- Invoice Item Details-->
                <div class="col-12 mt-20">
                    <table class="table table-boarder mt-20">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Item</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Appointment Confirmation</td>
                                <td>Appointment Time : {{ Carbon\Carbon::parse($data->start_time)->format('d M, Y H:i') }}  TO {{ Carbon\Carbon::parse($data->end_time)->format('H:i') }}</td>
                                <td>{{ number_format($data->fee, 2) }}</td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right pr-5" >Sub Total</th>
                                <th>{{ number_format($data->fee, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right pr-5">Tax</th>
                                <th>0</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right pr-5">Total</th>
                                <th>{{ number_format($data->fee, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>
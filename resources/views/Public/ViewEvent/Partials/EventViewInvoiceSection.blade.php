<section id="order_form" class="container">
    <div class="row">
        <h1 class="section_head">
            Order Details
        </h1>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="content event_view_order">
                <div class="table-layout">
                    <!-- Left / Bottom Side -->
                    <div class="col-lg-12 panel">
                        <!-- panel body -->
                        <div class="panel-body text-right">
                            <h4 class="semibold nm">{{{$order->first_name.' '.$order->last_name}}}</h4>
                            <p class="text-muted nm">17th June 2014</p>
                        </div>
                        <!-- panel body -->
                        <hr class="nm">
                        <!-- panel body -->
                        <div class="panel-body">
                            <ul class="list-table">
                                <li class="text-left">
                                    <h4 class="semibold nm">Invoice / December Period</h4>
                                    <p class="semibold text-muted nm">December 22nd, 2014 - October 8th, 2014</p>
                                </li>
                                <li class="text-right">
                                    <p class="semibold text-primary nm">Invoice ID : #{{$order->id}}</p>
                                </li>
                            </ul>
                        </div>
                        <!-- panel body -->
                        <!-- panel table -->
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th width="5%"></th>
                                        <th>Ticket Title</th>
                                        <th width="15%" class="text-center">Quantity</th>
                                        <th width="15%" class="text-center">Price</th>
                                        <th width="15%" class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->order_items as $order_item)
                                    <tr>
                                        <td class="valign-top text-center">1.</td>
                                        <td>
                                            <h5 class="semibold mt0 mb5">{{$order_item->title}}</h5>
                                        </td>
                                        <td class="valign-top text-center"><span class="bold">{{$order_item->quantity}}</span></td>
                                        <td class="valign-top text-center"><span class="bold">{{$order_item->unit_price}}</span></td>
                                        <td class="valign-top text-center"><span class="text-primary bold">€{{$order_item->unit_price * $order_item->quantity}}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!--/ panel table -->
                        <!-- panel footer -->
                        <div class="panel-footer">
                            <ul class="list-table pa10">
                                <li>
                                    <h5 class="semibold nm">SUB TOTAL</h5>
                                </li>
                                <li class="text-right">
                                    <h3 class="semibold nm">$10,140.00</h3>
                                </li>
                            </ul>
                        </div>
                        <!-- panel footer -->
                        <!-- panel footer -->
                        <div class="panel-footer">
                            <ul class="list-table pa10">
                                <li>
                                    <h5 class="semibold nm">TAXES &amp; FEES</h5>
                                </li>
                                <li class="text-right">
                                    <h3 class="semibold nm">$60.00</h3>
                                </li>
                            </ul>
                        </div>
                        <!-- panel footer -->
                        <!-- panel footer -->
                        <div class="panel-footer">
                            <ul class="list-table pa10">
                                <li>
                                    <h5 class="semibold nm">TOTAL PAYABLE</h5>
                                </li>
                                <li class="text-right">
                                    <h3 class="semibold nm text-success">$10,200.00</h3>
                                </li>
                            </ul>
                        </div>
                        <!-- panel footer -->
                    </div>
                    <!--/ Left / Bottom Side -->
                </div>
            </div>
        </div>
    </div>
</section>
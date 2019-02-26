/* author: mannu
      purpose: send mandate emailers 
      params : sip with topup
      created: 19/02/2019
      modified: 
      modified by:  */ 
public function SipTopUp()//$date = ''
{ //echo "ok"; die;
	if($date == '')  
     $date = date('Y-m-d');
  //fetching data from client orders of SIP  //1415972
 //$unique_no=array('SIP','xsip');
        $clientLedgerBalance        = [];
        $sip_day = date('d',strtotime($date));
        $params                     = array();
        $params['env']              = 'db';            
        $params['select_data']      = 'id,client_id,order_type,amc_code,order_id,scheme_code,amount,order_status,sip_reg_id,start_date,date_created,installment_amount,basket_order_id,created_by,source,order_mode,folio_number';
        $params['table_name']       = 'mf_client_order_mfi';   
        $params['where']            = TRUE;           
        $params['return_array']     = TRUE;  
        $params['where_in']         = TRUE;
        $params['where_in_field']   = 'order_type';
        $params['where_in_data']    = array('SIP','xsip');        
        $params['where_data']       = array( 'order_status' => '0','DAY(mf_client_order_mfi.start_date)'=>$sip_day,'with_topup'=> '0','start_date <='=> $date,'client_id'=>'DN31190');//,'client_id'=>'DN31190','start_date'=>date("Y-m-d",strtotime($date ." +1 day")),'
        $params['order_by']         = "date_created DESC"; 
        //$params['print_query_exit'] = true;//DAY(mf_client_order_mfi.start_date)
        $clientOrders               = $this->mf->get_table_data_with_type($params); 
        //x($clientOrders);
        foreach($clientOrders as $order)
        { 
        	/*if(!array_key_exists($order['client_id'],$clientLedgerBalance)){ 
                        $ledger_balance      =  $this->getLedgerBalance($order['client_id']);
                        if($ledger_balance){
                            //if(array_key_exists($order['client_id'],$lumpsum_client_data))
                                //$ledger_balance -= 10;//$lumpsum_client_data[$order['client_id']];

                            $clientLedgerBalance[$order['client_id']]  = $ledger_balance;
                        }
                        //$clientLedgerBalance['RP8689'] = '75898.6';
                    }

                  //x($clientLedgerBalance);
                    //total number of orders of perticular client
                    if(isset($client_orders_count[$order['client_id']]))
                        $add_amount = $this->atom_charges * ($client_orders_count[$order['client_id']] + 1);
                    else
                        $add_amount = $this->atom_charges;
                                       

                    // if client have sufficient balance to place order, ledger balance get diducted in array and added entry to process list
                    if(isset($clientLedgerBalance[$order['client_id']]) && ($clientLedgerBalance[$order['client_id']] + $add_amount) >= $order['installment_amount']){ 
                        
                        $bdl_balance_single = $clientLedgerBalance[$order['client_id']]; //before diduction ledger balance
                       
                        //total number of orders of perticular client
                        if(!isset($client_orders_count[$order['client_id']]))
                            $client_orders_count[$order['client_id']] = 1;
                        else
                            $client_orders_count[$order['client_id']] += 1;

                        $clientLedgerBalance[$order['client_id']] -= $order['installment_amount'];
                        
                         //added Rs +9 in ledger for atom charges it will be show minus amount in client ledger
                        if($bdl_balance_single < $order['installment_amount']){
                            $atomCharges[] = array('client_id' => $order['client_id'],'order_type'=>$order['order_type'],'basket_order_id'=>$order['basket_order_id'],
                                            'table_id'=>$order['id'],'reg_id'=>$order['sip_reg_id'],'process_date'=>date("Y-m-d",strtotime($date)),
                                            'ledger_balance'=>$bdl_balance_single,'order_amount'=>$order['installment_amount'],                                            
                                            'adl_balance'=>$clientLedgerBalance[$order['client_id']],'order_count'=>$client_orders_count[$order['client_id']],
                                            'amount_added'=>($add_amount),'amount_added_used'=>($order['installment_amount'] - $bdl_balance_single)
                                            ,'date_created'=>date("Y-m-d H:i:s")); 
                        }*/
                        
                        //echo "<pre>";var_dump($clientLedgerBalance[$order['client_id']],$client_orders_count[$order['client_id']]);
                        $process_list[]    = array('client_id'=>$order['client_id'],
                                    'order_type'    => 'lumpsum',//$order['order_type'],
                                    'order_id'      => 0,//$order['order_id']
                                    'sip_reg_id'    => $order['sip_reg_id'],
                                    'scheme_code'   => $order['scheme_code'],
                                    'amount'        => $order['installment_amount'],  
                                    'order_processed'=>'5',                                                    
                                    'order_date'    => date("Y-m-d",strtotime($order['start_date'])),
                                    'created_by'    => $order['created_by'],
                                    'source'        => $order['source'],
                                    'order_mode'    => $order['order_mode'],
                                    'folio_number'  => $order['folio_number'],
                                    'process_date'  => date("Y-m-d",strtotime($date)),
                                    'date_created'  => date("Y-m-d H:i:s")
                                );
                        //adding entry for process List
                        if(count($process_list) == 50){                            
                            $this->insertData('mf_order_process_list',$process_list);
                            $process_list           = [];
                        }// else not require to but adedd thier
                    /*}else{ //if client have insufficient balance in his ledger then SIP get skipped

                        if(isset($clientLedgerBalance[$order['client_id']]))
                            $reason = 'Ledger balance is not sufficient to place order';
                        else
                            $reason = 'Not getting Ledger Data';

                        //added for adding skip sip order to a table daily
                        $skipSip_details[] = array(
                            'client_id'  => $order['client_id'],    
                            'sip_reg_id' => $order['sip_reg_id'],
                            'order_id' => 0,//$order['order_id']
                            'installment_amount' => $order['installment_amount'],                        
                            'start_date' => $order['start_date'],
                            'process_date' => date('Y-m-d',strtotime($date)),
                            'reason'    => $reason,
                            'is_processed'=>'5',
                            'created_by'   => $order['created_by'],
                            'source'       => $order['source'],
                            'date_created' => date('Y-m-d H:i:s')
                        );

                        //adding entry for Skip Order Table
                        if(count($skipSip_details) == 50){                           
                            $this->insertData('mf_skip_sip_list',$skipSip_details);
                            $skipSip_details        = [];
                            
                        }
                    }*/
        }
        //adding remaining entries to sip list table
        if(!empty($skipSip_details)){          
            //$this->insertData('mf_skip_sip_list',$skipSip_details);//comment
            $skipSip_details        = [];            
        }
       
        //adding remaining entries to sip process list
        if(!empty($process_list)){          
            $this->insertData('mf_order_process_list',$process_list);
            $process_list           = [];
        }
        if(!empty($atomCharges)){
            //$this->atomChargesLogs($atomCharges); it comment to
        }

     

}
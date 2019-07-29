<?php
namespace Larapix;

use Request;

class DataTables{

    public $DataTables;

    /**
     * DataTables khusus Lyto
     */
    public function from($tb, $request, $config = true){

        $whereString  = [];
        $search = $request->search['value'];
        $length = ( $request->length ) ? $request->length : 10;
        $table  = $tb;
        $totalTable = $tb->count();
        $orderColumn= (int) $request->order[0]['column'];
        $orderDir   = $request->order[0]['dir'];
        foreach( $request->columns as $column ){
            if($column['searchable'] == 'true' && strlen($search) > 0 ):
                $search = str_replace('+','%',$search);
                $whereString[]  =  $column['data']. ' LIKE "%'.$search.'%"';
            endif;
        }

        if( ( count($whereString) > 0 ) && strlen($search) > 0):
            $where  = implode(' OR ', $whereString);
            $table->whereRaw($where);
        endif;

        if( $orderColumn !== 0 ):
            $orderable  = $request->columns[$orderColumn]['orderable'];
            if( $orderable == 'true' ):
                $getNameColumn  = $request->columns[$orderColumn]['data'];
                $table->orderBy($getNameColumn, $orderDir);
            endif;
        endif;

        $tableFinal = $table->paginate($length);
        $start      = $tableFinal->firstItem();
        $colName    = $this->getColumnName($tb, false);
        $columns    = $getData = [];

        foreach( $tableFinal as $index => $row ){
            foreach( $colName as $column ){
                if($config == true):
                    $columns[$index][$column] = $row->$column;
                else:
                    $columns[$index][] = $row->$column;
                endif;
            }
        }

        foreach($columns as $val){
            if($config == true):
                $getData[] =  array_merge(['no' => $start++], $val );
            else:
                $getData[] =  array_merge([$start++], $val );
            endif;
        }

        /**
         * Rusult for DataTables
         */
        $result = collect([
            'draw' => (int) $request->draw,
            'recordsTotal' => $totalTable,
            'recordsFiltered' => $tableFinal->total(),
            'nextPageUrl' => $tableFinal->nextPageUrl(),
            // 'column_name' => $colName,
            'data' => collect($getData),
        ]);
        $this->DataTables = $result;
        return $this;
    }

    /**
     * get Column Name from Table
     */
    public function getColumnName($tb, $type = true ){
        $colName    = $colRaw   = [];
        $i  = 2;
        foreach( $tb->get() as $column ){
            foreach($column as $name => $value){
                $colRaw[$name] = $name;
            }
        }
        foreach($colRaw as $val){
            $colName[$i++] = $val;
        }
        $column = ( $type ) ? $colRaw : $colName;
        return $column;
    }

    /**
     * Edit Column
     */
    public function editColumn($col, $parFunction ){

        try{
            $getColumn = $this->DataTables['column_name'][$col];
            foreach($this->DataTables['data'] as $key => $value){
                foreach($value as $key_data => $val_data){
                    /**
                    * Check if Key Integer or String
                    */
                    $getKey = ( is_int($key_data) ) ?  true : false ;

                    if( $getKey == true && ( $key_data == $col ) ){
                        $data_col = call_user_func( $parFunction, collect($value) );

                    }elseif( $getKey == false && strpos($key_data, $getColumn ) !== false ){
                        $data_col = call_user_func( $parFunction, collect($value) );

                    }else{
                        $data_col = $val_data;
                    }

                        $var[$key][$key_data] = $data_col;

                }
            }

        $dataColection  = $this->DataTables;
        $DataTables     = $dataColection->merge(['data' => $var]);
        $data = $DataTables;

        }catch(\Exception $e){
            $data = [
            'table' => $col,
            'error' => $e->getMessage(),
            ];
        }

        $this->DataTables = $data;
        return $this;

    }

    public function build($par = false){

        if($par):
            return $this->DataTables;
        else:
            return response()->json(['error' => 'Please set \'True\' in make() Method'], 400);
        endif;
    }

    public function __call($name, $arguments){

        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $arguments);
        }

        return trigger_error('Call to undefined method ' . __CLASS__ . '::' . $name . '()', E_USER_ERROR);
    }

}

<?php

class Travel
{

    public function getTravel(Type $var = null)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://5f27781bf5d27e001612e057.mockapi.io/webprovise/travels',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        
        $response = curl_exec($curl);
        if(curl_errno($curl)){
            echo 'Request Error:' . curl_error($curl);
        }else{
            return $this->customTravels(json_decode($response));
        }
        curl_close($curl);
      
        
    }

    private function customTravels($listTravel = [])
    {
        $newListTravel = [];
       if($listTravel){
      
            foreach ($listTravel as $lk => $value) {
                $newListTravel[$value->companyId]['data'][] = $value;
                isset($newListTravel[$value->companyId]['totalPrice'])? $newListTravel[$value->companyId]['totalPrice'] += $value->price : $newListTravel[$value->companyId]['totalPrice'] = $value->price;
            }
       }

       return $newListTravel;
    }


}

class Company
{


    public function getCompany(Type $var = null)
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://5f27781bf5d27e001612e057.mockapi.io/webprovise/companies',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        
        $response = curl_exec($curl);
        if(curl_errno($curl)){
            echo 'Request Error:' . curl_error($curl);
        }else{
            return json_decode($response);
        }
        curl_close($curl);
    }

   
}

class TestScript
{
    public function execute()
    {
        $start = microtime(true);
        $travel = new Travel();
        $company = new Company();
        $listCompany = $company->getCompany();
        $listCompanyWithPrice = $travel->getTravel();
        $newTravelCost = [];

        foreach ($listCompany as $key => $value) {
            if(isset($listCompanyWithPrice[$value->id])){
                $newTravelCost[] =  [
                    'id' => $value->id,
                    'name' => $value->name,
                    'parentId' => $value->parentId,
                    'cost' => $listCompanyWithPrice[$value->id]['totalPrice'],
                    'children' => $listCompanyWithPrice[$value->id]['data']
                ];
            }else{
                $newTravelCost[] =  [
                    'id' => $value->id,
                    'name' => $value->name,
                    'cost' => 9696,
                    'children' => []
                ];
            }
          
        }

        $result = [];
        foreach ($newTravelCost as $key => $value) {
            $found_key = array_search($value['parentId'], array_column($newTravelCost, 'id'));
            $result[$value['parentId']]['id'] =  $value['parentId'];
            $result[$value['parentId']]['name'] =  $newTravelCost[$found_key ]['name'];
            isset($result[$value['parentId']]['cost']) ? $result[$value['parentId']]['cost'] +=  $value['cost'] : $result[$value['parentId']]['cost'] = $value['cost'];
            $result[$value['parentId']]['children'][] =  $value;


        }
        echo '<pre>';
        print_r($result);
        echo '</pre>';
        echo 'Total time: '.  (microtime(true) - $start);
    }
}

(new TestScript())->execute();
<?php

namespace App\Traits;

trait Relatives
{
    /**
     * Isolate the ascendants related to the subject(clan member).
     *
     * @param array $ascendants The list of all ascendants
     * @param string $subject The id of the subject(clan member)
     * @return array List of ascendants related to the subject
     */
    protected function isolateRelated($ascendants, $subject){
        $iteration = 0;
        $isolated_ascendants = [];
        $temp = $ascendants;
        $begin = $k = 0;
        if($subject !== null) array_push($isolated_ascendants, $subject);
        do{
            $repeat = false;
            $remove = [];// indices of items to remove
            if($k == 0){
                $n = count($temp);// iterations to perform
                for ($i=0; $i < $n; $i++) {$iteration++;
                    if(strval($subject) == strval($temp[$i]->member_id)){
                        if($repeat == false){
                            $repeat = true;
                            $begin = 1;
                        }
                        array_push($isolated_ascendants, $temp[$i]->ascendant_id);
                        array_push($remove, $i);
                    }
                }
            }else{
                $m = count($isolated_ascendants);
                $n = count($temp);
                for ($i=$begin; $i < $m; $i++) {
                    for ($j=0; $j < $n; $j++) {$iteration++;
                        if(strval($isolated_ascendants[$i]) == strval($temp[$j]->member_id)){
                            if($repeat == false){
                                $repeat = true;
                                $begin = ($m - 1);
                            }
                            array_push($isolated_ascendants, $temp[$j]->ascendant_id);
                            array_push($remove, $j);
                        }
                    }
                }
            }
            $n = count($remove);
            for ($j=0; $j < $n; $j++) {
                array_splice($temp, $remove[$j], 1);
                for ($h=($j+1); $h < $n; $h++) {
                    if($remove[$j] < $remove[$h]) array_set($remove, $h, ($remove[$h] - 1));
                }
            }
            array_splice($remove, 0, count($remove));
            $k++;
        }while($repeat == true);
        // dd($iteration);
        return $isolated_ascendants;
    }

    /**
     * Isolate the ascendants related to the parents of a subject(clan member).
     *
     * @param array $ascendants The list of all ascendants
     * @param array $parents The list ids of the parents of a subject(clan member)
     * @return array List of ascendants related to the subject
     */
    protected function isolateRelatedThroughParents($ascendants, $parents){
        $isolated_ascendants = [];
        if(count($parents) > 0){
            for ($i=0; $i < count($parents); $i++) {
                $iso_temp = $this->isolateRelated($ascendants, $parents[$i]);
                if(count($iso_temp) > 0) array_push($isolated_ascendants, ...$iso_temp);
                array_splice($iso_temp, 0, count($iso_temp));
            }
        }
        return $isolated_ascendants;
    }

    /**
     * Isolate the ascendants related to the spouse of a subject(clan member).
     *
     * @param array $ascendants The list of all ascendants
     * @param array $spouse The id of the spouse of a subject(clan member)
     * @return array List of ascendants related to the subject
     */
    public function isolateRelatedThroughSpouses($ascendants, $spouses){
        $isolated_ascendants = [];
        if(count($spouses) > 0){
            for ($i=0; $i < count($spouses); $i++) {
                $iso_temp = $this->isolateRelated($ascendants, $spouses[$i]);
                if(count($iso_temp) > 0) array_push($isolated_ascendants, ...$iso_temp);
                array_splice($iso_temp, 0, count($iso_temp));
            }
        }
        return $isolated_ascendants;
    }

    /**
     * Tells whether two people are related using their respective ascendants.
     *
     * @param array $ascendants The list of all ascendants
     * @param array $parents The list of ids of the parents of the first person
     * @param string $subject The id of the second person(subject)
     * @param array $spouses The list of ids of the spouses of the second person
     * @return boolean true if the two people are relatives, false otherwise
     */
    protected function isRelated($ascendants, $parents, $subject, $spouses){
        $primary_ascendants = $this->isolateRelatedThroughParents($ascendants, $parents);
        $subject_ascendants = $this->isolateRelated($ascendants, $subject);
        $iteration = 0;
        do{
            if($iteration == 1)$subject_ascendants = $this->isolateRelatedThroughSpouses($ascendants, $spouses);
            for ($i=0; $i < count($primary_ascendants); $i++) {
                for ($j=0; $j < count($subject_ascendants); $j++) {
                    if($primary_ascendants[$i] == $subject_ascendants[$j]){
                        return true;
                    }
                }
            }
            if($iteration == 1)return false;
            $iteration++;
        }while((count($spouses) > 0));
        return false;

    }
}

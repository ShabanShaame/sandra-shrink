<?php


namespace App;


use SandraCore\ConceptManager;
use SandraCore\System;

class ConceptExplorer
{

    public function loadConcept($conceptId,System $sandra,ShrinkConsultation $shrinkConsultation){

        $conceptManager = new ConceptManager(1,$sandra);
        $result = $conceptManager->getConceptsFromArray(array($conceptId));
        $triplets = $conceptManager->getTriplets();


        $concept = $sandra->conceptFactory->getConceptFromShortnameOrId($conceptId);

        $refs = $concept->getReferences();



        foreach($refs ? $refs : array() as $conceptId => $conceptData){
            $group="concept".$conceptId;
            $message = "Analysing Concept ".$conceptId ;

            $shrinkConsultation->sendResponse($group,$message);
            $shrinkConsultation->sendResponse("separator","----------------");

            //Do we have references ?

            foreach($conceptData ? $conceptData : array() as $entityId => $entityData){

                $message = "Entity ".$entityId ;
                $group="entity".$entityId;
               // $shrinkConsultation->sendResponse($group,$message);

                $verbConceptId = $entityData['idConceptLink'] ;
                $linkShortName = $sandra->systemConcept->getSCS($verbConceptId);
                $targetConceptId = $entityData['idConceptTarget'] ;
                $targetShortName = $sandra->systemConcept->getSCS($targetConceptId);
                $shrinkConsultation->sendResponse($group,"$message : $linkShortName ($verbConceptId) -> $targetShortName ($targetConceptId)");
                foreach($entityData ? $entityData : array() as $entityKey => $reference){

                    if (!is_int($entityKey)) continue ;

                    $refName = $sandra->systemConcept->getSCS($entityKey);
                    $message = "$refName ($entityKey) = $reference" ;
                    $group="ref".$entityKey;
                    $shrinkConsultation->sendResponse($group,$message);


                }

            }


        }

        $shrinkConsultation->sendResponse("separator","----------------");

        foreach($triplets ? $triplets : array() as $conceptId => $conceptData){

 foreach($conceptData ? $conceptData : array() as $verbId => $targetArray){

                $group="entityLink".$verbId;

                foreach($targetArray ? $targetArray : array() as  $targetId){
                    $verbName = $sandra->systemConcept->getSCS($verbId);
                    $targetName = $sandra->systemConcept->getSCS($targetId);
                    $message = "$verbName ($verbId) -> $targetName ($targetId)" ;

                    $shrinkConsultation->sendResponse($group,$message);
                }

            }


        }

        return $triplets ;



    }

}

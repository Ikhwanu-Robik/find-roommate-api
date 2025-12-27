<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Similarity Threshold
    |--------------------------------------------------------------------------
    |
    | how many percent should the output bio match the criterion bio?
    |
    | Each profile will have some tags attached to them based on their bio.
    | When looking for a match, the app will try to find profiles whose tags
    | has x number of similar tags with the tags of the inputted criterion bio.
    | That x number is acquired from the formula:
    | x = ceiling(bio_similarity_threshold * length(criterion_bio_tags))
    | 
    | bio_similarity_threshold is a percentage represented using decimal.
    |
    */
    
    'bio_similarity_threshold' => 0.8,
];
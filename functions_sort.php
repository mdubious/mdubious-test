<?php

/**
 *  * 000 get_set_nid_product_ids
 *  * Provide nid or pns, returns product_id 111 * 
 *  */
function get_set_nid_product_ids( $use_cache = true ) {
  $cid = __function__;
  $gs_nids_namespaces = get_set_nids_namespaces();
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data; // return $cached->data;
  } else { //
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT entity_id, field_product_first FROM {field_data_field_product}" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $nid = $item->entity_id;
    if( $item->field_product_first ) {
      $ret[$nid] = $item->field_product_first;
      if( !empty( $gs_nids_namespaces[$nid] ) ) {
        $ret[$gs_nids_namespaces[$nid]] = $item->field_product_first;
      }
    }
  }
  return $ret;
}


/**
 *  * 000 get_set_nid_summit_speakers
 *  * Provide summit nid or pns, returns array of faculty nids 111 * 
 *  */
function get_set_nid_summit_speakers( $use_cache = true ) {
  $cid = __function__;
  $gs_nids_namespaces = get_set_nids_namespaces();
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data;
  } else { // $data
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT f.entity_id, field_summit_ref_nid, field_faculty_ref_nid
                FROM {field_data_field_faculty_ref} f 
                INNER JOIN {field_data_field_summit_ref} s ON s.entity_id = f.entity_id
                WHERE f.bundle = 'summit_reference'" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $snid = $item->field_summit_ref_nid;
    $ret[$snid][] = $item->field_faculty_ref_nid;
    if( !empty( $gs_nids_namespaces[$snid] ) ) {
      $ret[$gs_nids_namespaces[$snid]][] = $item->field_faculty_ref_nid;
    }
  }
  return $ret;
}

function get_set_summits_speakers( $use_cache = true ) {
  return get_set_nid_summit_speakers( $use_cache );
}

/**
 * 000 get_set_nid_speaker_bonuses
 * Provide faculty nid, returns array summit nid => bonus nid 111 * 
 */
function get_set_nid_speaker_bonuses( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data;
  } else { // $data
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT f.entity_id, field_summit_ref_nid, field_faculty_ref_nid
                FROM {field_data_field_faculty_ref} f 
                INNER JOIN {field_data_field_summit_ref} s ON s.entity_id = f.entity_id
                WHERE f.bundle = 'files_handler'" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $fac_nid = $item->field_faculty_ref_nid;
    $snid = $item->field_summit_ref_nid;
    $ret[$fac_nid][$snid] = $item->entity_id;
  }
  return $ret;
}

function get_set_speaker_bonuses( $use_cache = true ) {
  return get_set_nid_speaker_bonuses( $use_cache );
}

/**
 *  * 000 get_set_speaker_offers
 * Provide faculty nid, returns array summit nid => speaker offers nid 111 * 
 *  */
function get_set_nid_speaker_offers( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data;
  } else {
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT fareef.entity_id,
    field_faculty_ref_nid,
    field_summit_ref_nid,
    field_special_offer_first,
    field_special_offer_second,
    fareef.delta AS fareef,
    spofer.delta AS spofer
    FROM {field_data_field_faculty_ref} fareef        
    INNER JOIN {field_data_field_summit_ref} sureef ON fareef.entity_id = sureef.entity_id
    INNER JOIN {field_data_field_special_offer} spofer ON fareef.entity_id = spofer.entity_id  " );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $fac_nid = $item->field_faculty_ref_nid;
    $ret[$fac_nid][$item->field_summit_ref_nid][$item->spofer]['offer_url'] = $item->field_special_offer_first;
    $ret[$fac_nid][$item->field_summit_ref_nid][$item->spofer]['offer_notes'] = $item->field_special_offer_second;
  }
  return $ret;
}

function get_set_speaker_offers( $use_cache = true ) {
  return get_set_nid_speaker_offers( $use_cache );
}

/**
 *  * 000 get_set_faculty_nid_refs
 *  * Provide faculty nid, returns refs array 111 * 
 *  */
function get_set_faculty_nid_refs( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data;
  } else { // $data
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT bundle, field_faculty_ref_nid, entity_id FROM {field_data_field_faculty_ref} ORDER BY entity_id DESC" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $fac_nid = $item->field_faculty_ref_nid;
    $ret[$fac_nid][$item->bundle][] = $item->entity_id;
  }
  return $ret;
}

function get_set_faculty_refs( $use_cache = true ) {
  return get_set_faculty_nid_refs( $use_cache );
}



/**
 *  * 000 get_set_nid_faculty_ref
 *  * Provide product nid, returns single faculty nid 111 * 
 *  */
function get_set_nid_faculty_ref( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data;
  } else { // $data
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT bundle, field_faculty_ref_nid, entity_id FROM {field_data_field_faculty_ref} ORDER BY entity_id DESC" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $ret[$item->entity_id] = $item->field_faculty_ref_nid;
  }
  return $ret;
}

function get_set_nids_faculty_refs( $use_cache = true ) {
  return get_set_nid_faculty_ref( $use_cache );
}

/**
 *  * 000 get_set_nid_summit_host_refs
 *  * Provide summit host nid, returns their refs array 111 * 
 *  */
function get_set_nid_summit_host_refs( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data;
  } else { // $data
    $data = db_query( "SELECT bundle, field_host_nid, entity_id FROM {field_data_field_host} ORDER BY entity_id DESC" );
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $fac_nid = $item->field_host_nid;
    $ret[$fac_nid][$item->bundle][] = $item->entity_id;
  }
  return $ret;
}

function get_set_host_refs( $use_cache = true ) {
  return get_set_nid_summit_host_refs( $use_cache );
}


/**
 *  * 000 get_set_nid_summit_refs
 *  * Provide [session, asset, package] node nid, returns summit ref nid 111 * 
 *  */
function get_set_nid_summit_refs( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data;
  } else { // $data
    $data = db_query( "SELECT field_summit_ref_nid, entity_id FROM {field_data_field_summit_ref}" );
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $nid = $item->entity_id;
    $ret[$nid] = $item->field_summit_ref_nid;
  }
  return $ret;
}

function get_set_nids_summit_refs( $use_cache = true ) {
  return get_set_nid_summit_refs( $use_cache );
}

function get_set_summit_refs( $use_cache = true ) {
  return get_set_nid_summit_refs( $use_cache );
}


/**
 *  * 000 get_set_nid_subgroups_names
 *  * Provide product nid, returns array: subgroup_nid => subgroup_name 111 * 
 *  */
function get_set_nid_subgroups_names( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data;
  } else { // $data
    $data = db_query( "SELECT entity_id, field_subgroups_first, field_subgroups_second FROM {field_data_field_subgroups}" );
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $num = $item->field_subgroups_first;
    $ret[$item->entity_id][$num] = $item->field_subgroups_second;
  }
  return $ret;
}

/**
 *  * 000 get_set_nid_node_types
 *  * Provide node nid, returns node-type 111 * 
 *  */
function get_set_nid_node_types( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data;
  } else { // $data
    $data = db_query( "SELECT type, nid FROM {node}" );
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $ret[$item->nid] = $item->type;
  }
  return $ret;
}


/**
 * 000 get_set_type_datestamps
 * Provide type [course, summit, session, event], returns array nid => datestamp 111  
 */
function get_set_type_datestamps( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data;
  } else { // $data
    $data = db_query( "SELECT nid, type, field_course_date_value, field_datetime_value, field_date_value
                FROM {node} n
                LEFT JOIN {field_data_field_course_date} c ON n.nid = c.entity_id
                LEFT JOIN {field_data_field_datetime} d ON n.nid = d.entity_id
                LEFT JOIN {field_data_field_date} e ON n.nid = e.entity_id" );
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $nid = $item->nid;
    $type = $item->type;
    $field_course_date_value = $item->field_course_date_value;
    $field_datetime_value = $item->field_datetime_value;
    $field_date_value = $item->field_date_value;
    if( $field_course_date_value ) {
      $ret[$type][$nid] = $field_course_date_value;
    }
    if( $field_datetime_value ) {
      $ret[$type][$nid] = $field_datetime_value;
    }
    if( $field_date_value ) {
      $ret[$type][$nid] = $field_date_value;
    }
  }
  return $ret;
}

function get_set_datestamps( $use_cache = true ) {
  return get_set_type_datestamps( $use_cache );
}

/**
 *  * 000 get_set_nid_datestamp
 *  * Provide nid, returns UNIX datestamp 111 * 
 *  */
function get_set_nid_datestamp( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data;
  } else { // $data
    $data = db_query( "SELECT nid, type, field_course_date_value, field_datetime_value, field_date_value
                FROM {node} n
                LEFT JOIN {field_data_field_course_date} c ON n.nid = c.entity_id
                LEFT JOIN {field_data_field_datetime} d ON n.nid = d.entity_id
                LEFT JOIN {field_data_field_date} e ON n.nid = e.entity_id" );
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $nid = $item->nid;
    $type = $item->type;
    $field_course_date_value = $item->field_course_date_value;
    $field_datetime_value = $item->field_datetime_value;
    $field_date_value = $item->field_date_value;
    if( $field_course_date_value ) {
      $ret[$nid] = $field_course_date_value;
    }
    if( $field_datetime_value ) {
      $ret[$nid] = $field_datetime_value;
    }
    if( $field_date_value ) {
      $ret[$nid] = $field_date_value;
    }
  }
  return $ret;
}


/**
 *  * 000 get_set_faculty_namespaces
 *  * Provide faculty nid, returns array 111 * 
 *  */
function get_set_faculty_namespaces( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data;
  } else { // $data
    $data = db_query( "SELECT f.field_faculty_ref_nid, s.field_summit_ref_nid, ns.entity_id
                FROM {field_data_field_faculty_ref} f
                LEFT JOIN {field_data_field_summit_ref} s ON f.entity_id = s.entity_id
                LEFT JOIN {field_data_field_nameseries_ref} ns ON f.entity_id = ns.field_nameseries_ref_nid
                ORDER BY ns.entity_id DESC, s.entity_id DESC" );
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  $get_set_nids_namespaces = get_set_nids_namespaces( $use_cache );
  foreach( $result as $item ) {
    $fac_nid = $item->field_faculty_ref_nid;
    if( $fac_nid ) {
      if( $course_nid = $item->entity_id ) {
        $ret[$fac_nid][$course_nid]['course'] = $get_set_nids_namespaces[$course_nid];
      }
      if( $field_summit_ref_nid = $item->field_summit_ref_nid ) {
        $ret[$fac_nid][$field_summit_ref_nid]['summit'] = $get_set_nids_namespaces[$field_summit_ref_nid];
      }
    }
  }
  return $ret;
}


/**
 *  * 000 get_set_nids_faculty_names
 *  * Provide product nid or pns, returns faculty names 111 * 
 *  */
function get_set_nids_faculty_names( $use_cache = true ) {
  $cid = __function__;

  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data; // return $cached->data;
  } else {
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT 
            fac_node.nid AS faculty_nid,
            fac_node.title,
            field_preferred_name_value AS preferred,
            fr.entity_id AS ref_nid,
            cr.entity_id AS node_ref_nid,
            field_course_ref_nid AS course_nid,
            field_namespace_value
            FROM {node} fac_node
            LEFT JOIN {field_data_field_preferred_name} p ON p.entity_id = fac_node.nid
            LEFT JOIN {field_data_field_faculty_ref} fr ON fr.field_faculty_ref_nid = fac_node.nid
            LEFT JOIN {field_data_field_course_ref} cr ON cr.field_course_ref_nid = fr.entity_id
                LEFT JOIN {field_data_field_namespace} nsp ON nsp.entity_id = cr.field_course_ref_nid
            WHERE fr.bundle = 'course' OR fr.bundle = 'asset' OR fr.bundle = 'session'
            AND fac_node.type = 'faculty'" ); //  ORDER BY fr.entity_id DESC LIMIT 1000
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $faculty_nid = $item->faculty_nid;
    $node_ref_nid = $item->node_ref_nid;
    $course_nid = $item->course_nid;
    $course_refs[$node_ref_nid] = $course_nid;
    $ref_nid = $item->ref_nid;
    $pnses[$ref_nid] = $item->field_namespace_value;
    $ref_faculty_refs[$ref_nid][] = $faculty_nid;
    $faculty_name = $item->preferred;
    if( !$faculty_name ) {
      $faculty_name = $item->title;
    }
    $rets[$faculty_nid] = $faculty_name;
  } //foreach

  foreach( $ref_faculty_refs as $ref_nid => $xxx ) {
    $names = $name = null;
    $names_array = array();
    $pns = $pnses[$ref_nid];
    foreach( $xxx as $key => $faculty_nid ) {
      if( !empty( $rets[$faculty_nid] ) ) {
        $names_array[$rets[$faculty_nid]] = $rets[$faculty_nid];
      }
    } //foreach
    foreach( $names_array as $name ) {
      $names .= $name . ' & ';
    } //foreach
    $rets[$ref_nid] = substr( $names, 0, -3 );
    $rets[$pns] = substr( $names, 0, -3 );
  } //foreach

  foreach( $course_refs as $ref_nid => $course_nid ) {
    if( empty( $rets[$ref_nid] ) && !empty( $rets[$course_nid] ) ) {
      $rets[$ref_nid] = $rets[$course_nid];
      $pns = $pnses[$course_nid];
      $rets[$pns] = $rets[$course_nid];
    }
  } //foreach

  return $rets;
}

/**
 *  * 000 get_set_namespaces_faculty_names
 *  * Provide pns, returns faculty names 111 * 
 *  */
function get_set_namespaces_faculty_names( $use_cache = true ) {
  return get_set_nids_faculty_names( $use_cache );
}


/**
 *  * 000 get_set_faculty_names_nids
 *  * Provide faculty name (could be a spelling problem), returns faculty nid 111 * 
 *  */
function get_set_faculty_names_nids( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data; // return $cached->data;
  } else {
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT nid, title FROM {node} WHERE type = 'faculty'" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $faculty_nid = $item->nid;
    $faculty_name = $item->title;
    $rets[$faculty_name] = $faculty_nid;
  }
  return $rets;
}


/**
 *  * 000 get_set_faculty_nids_names
 *  * Provide faculty nid 111 * 
 *  */
function get_set_faculty_nids_names( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data; // return $cached->data;
  } else {
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT nid, title FROM {node} WHERE type = 'faculty'" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $faculty_nid = $item->nid;
    $faculty_name = $item->title;
    $rets[$faculty_nid] = $faculty_name;
  }
  return $rets;
}


/**
 * 000 get_set_faculty_items
 * Provide faculty nid, returns items array 111
 */
function get_set_faculty_items( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data; // return $cached->data;
  } else {
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT nid, title, field_preferred_name_value, field_firstname_value, field_lastname_value, field_tagline_value, field_bio_value, field_website_first, field_website_second, a.delta
                FROM {node} n
                LEFT JOIN {field_data_field_preferred_name} b ON nid = b.entity_id
                LEFT JOIN {field_data_field_firstname} f ON nid = f.entity_id
                LEFT JOIN {field_data_field_lastname} l ON nid = l.entity_id
                LEFT JOIN {field_data_field_tagline} c ON nid = c.entity_id
                LEFT JOIN {field_data_field_bio} a ON nid = a.entity_id
                LEFT JOIN {field_data_field_website} w ON nid = w.entity_id
                WHERE type = 'faculty'" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $nid = $item->nid;
    $title = $item->title;
    $field_preferred_name_value = $item->field_preferred_name_value;
    $firstname = $item->field_firstname_value;
    $lastname = $item->field_lastname_value;
    $field_tagline_value = $item->field_tagline_value;
    $field_bio_value = $item->field_bio_value;
    $field_website_first = $item->field_website_first;
    $field_website_second = $item->field_website_second;
    $get_set_faculty_items[$nid]['name'] = $title;
    if( !$field_preferred_name_value ) {
      $firstname && $lastname ? $field_preferred_name_value = $firstname . ' ' . $lastname : $field_preferred_name_value = $title;
    }
    $get_set_faculty_items[$nid]['preferred'] = $field_preferred_name_value;
    $get_set_faculty_items[$nid]['tagline'] = $field_tagline_value;
    if( isset( $item->delta ) && $item->delta == 0 ) {
      $get_set_faculty_items[$nid]['bio'] = $field_bio_value;
    }
    if( !$field_website_second ) {
      $get_set_faculty_items[$nid]['website'][$field_website_first] = $field_website_first;
    } else {
      $get_set_faculty_items[$nid]['website'][$field_website_second] = $field_website_first;
    }
  } // foreach

  return $get_set_faculty_items;
}


/**
 *  * 000 get_set_sys_images
 *  * Provide pns, returns array of images 111 * 
 *  */
function get_set_sys_images( $use_cache = true ) {
  $basepath = 'sites/files/sys_images/';
  $folders = _get_just_folders( $basepath );
  if( !empty( $folders ) ) {
    foreach( $folders as $fkey => $foldername ) {
      $pns = $foldername;
      $folderpath = $basepath . $foldername . '/';
      $files = _get_just_files( $folderpath );
      if( !empty( $files ) ) {
        foreach( $files as $fpkey => $filename ) {
          $filepath = $folderpath . $filename;
          $ret_array[$pns][$filename] = $filepath;
        } //foreach
      }
    } //foreach
  }
  return $ret_array;
}


function get_set_course_titles( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data; // return $cached->data;
  } else { // $data
    $ret = array( 'Select' => 'Select' );
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT entity_id, field_namespace_value FROM {field_data_field_namespace} WHERE bundle = 'course' ORDER BY entity_id DESC" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $nid = $item->entity_id;
    $ret[$nid] = $item->field_namespace_value;
  }
  return $ret;
}

function get_set_summit_titles( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data; // return $cached->data;
  } else { // $data
    $ret = array( 'Select' => 'Select' );
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT entity_id, field_namespace_value FROM {field_data_field_namespace} WHERE bundle = 'summit' ORDER BY entity_id DESC" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $nid = $item->entity_id;
    $ret[$nid] = $item->field_namespace_value;
  }
  return $ret;
}

function get_set_package_titles( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data; // return $cached->data;
  } else { // $data
    $ret = array( 'Select' => 'Select' );
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT nid, title FROM {node} WHERE type = 'package' ORDER BY nid DESC" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $nid = $item->nid;
    $ret[$nid] = $item->title;
  }
  return $ret;
}


/**
 *  * 000 get_set_nid_nameseries_ref
 *  * Provide nid [summit, course], returns nameseries nid 111 * 
 *  */
function get_set_nid_nameseries_ref( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data; // return $cached->data;
  } else {
    $data = db_query( "SELECT entity_id, field_nameseries_ref_nid FROM {field_data_field_nameseries_ref}" );
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $item_nid = $item->entity_id;
    $nameseries_ref = $item->field_nameseries_ref_nid;
    $ret[$item_nid] = $nameseries_ref;
  }
  return $ret;
}

/**
 * 000 get_set_nid_db
 * provide summit nid, return single db name 111
 */
function get_set_nid_db( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data;
  } else {
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT entity_id, field_source_db_value FROM {field_data_field_source_db}" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  $ret = array();
  foreach( $result as $item ) {
    $ret[$item->entity_id] = $item->field_source_db_value;
  }
  return $ret;
}

function get_set_nids_dbs( $use_cache = true ) {
  return get_set_nid_db( $use_cache );
}


/**
 *  * 000 get_set_nid_course_ref
 *  * Provide nid [asset, session, package, event], returns single course ref 111 * 
 *  */
function get_set_nid_course_ref( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data; // return $cached->data;
  } else {
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT entity_id, field_course_ref_nid FROM {field_data_field_course_ref}" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $nid = $item->entity_id;
    $ret[$nid] = $item->field_course_ref_nid;
  }
  return $ret;
}

function get_set_nids_course_refs( $use_cache = true ) {
  return get_set_nid_course_ref( $use_cache );
}


/**
 *  * 000 get_set_course_nid_refs
 *  * Provide course nid, returns array refs [asset, session, package, event] 111 * 
 *  */
function get_set_course_nid_refs( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data; // return $cached->data;
  } else {
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT entity_id, field_course_ref_nid FROM {field_data_field_course_ref}" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $nid = $item->entity_id;
    $course_ref_nid = $item->field_course_ref_nid;
    $ret[$course_ref_nid][] = $nid;
  }
  return $ret;
}

function get_set_course_refs_nids( $use_cache = true ) {
  return get_set_course_nid_refs( $use_cache );
}

/**
 *  * 000 get_set_course_nid_event_refs
 *  * Provide course nid, returns array event refs 111 * 
 *  */
function get_set_course_nid_event_refs( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data; // return $cached->data;
  } else {
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT entity_id, field_course_ref_nid FROM {field_data_field_course_ref} WHERE bundle = 'event'" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $nid = $item->entity_id;
    $course_ref_nid = $item->field_course_ref_nid;
    $ret[$course_ref_nid] = $nid;
  }
  return $ret;
}

function get_set_course_refs_event_nids( $use_cache = true ) {
  return get_set_course_nid_event_refs( $use_cache );
}


/**
 *  * 000 get_set_namespaces_namespaces
 *  * Doesn't take param, just returns array namespaces 111 * 
 *  */
function get_set_namespaces_namespaces( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data;
  } else {
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT entity_id, field_namespace_value FROM {field_data_field_namespace} ORDER BY entity_id DESC" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  foreach( $result as $item ) {
    $namespace = trim( $item->field_namespace_value );
    $ret[$namespace] = $namespace;
  }
  return $ret;
}



/**
 * 000 get_set_active_course_nids
 * Doesn't take param, just returns ACTIVE courses: Not started yet & closed AND Not ended & open 111
 */
function get_set_active_course_nids( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data;
  } else {
    $time = time();
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT d.entity_id, field_course_date_value, field_course_date_value2 
  FROM {field_data_field_course_date} d
  INNER JOIN {field_data_field_status} s ON s.entity_id = d.entity_id
  WHERE d.bundle = 'course' AND field_course_date_value2 > $time 
  AND ( field_status_value = 2 or ( field_course_date_value > $time and field_status_value = 1  ) )
  ORDER BY field_course_date_value DESC" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  $ret = array();
  foreach( $result as $item ) {
    $nid = $item->entity_id;
    $ret[] = $nid;
  }
  return $ret;
}



/**
 * 000 get_set_active_open_course_nids
 * Doesn't take param, just returns ACTIVE courses: Just open-for-reg courses 111
 */
function get_set_active_open_course_nids( $use_cache = true ) {
  $cid = __function__;
  if( $use_cache && ( $cached = cache_get( $cid, 'cache' ) ) ) {
    $result = $cached->data;
  } else {
    $time = time();
    db_set_active( 'd7_faculty_summits' );
    $data = db_query( "SELECT d.entity_id, field_course_date_value, field_course_date_value2 
  FROM {field_data_field_course_date} d
  INNER JOIN {field_data_field_status} s ON s.entity_id = d.entity_id
  WHERE d.bundle = 'course' AND field_course_date_value2 > $time 
  AND field_status_value = 2
  ORDER BY field_course_date_value DESC" );
    db_set_active();
    $result = $data->fetchAll();
    cclog( $cid );
    cache_set( $cid, $result, 'cache' );
  }
  $ret = array();
  foreach( $result as $item ) {
    $nid = $item->entity_id;
    $ret[] = $nid;
  }
  return $ret;
}



?>
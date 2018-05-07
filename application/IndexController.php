<?php
namespace blog;

require_once __DIR__ . '/../domain/CategoryRepo.php';
require_once __DIR__ . '/../domain/OptionsRepo.php';
require_once __DIR__ . '/dto/BlogInfoDTO.php';

class IndexController {

  private $categoryRepo;
  private $optionsRepo;

  public function __construct(CategoryRepo $categoryRepo, OptionsRepo $optionsRepo){                                           
    $this->categoryRepo = $categoryRepo;
    $this->optionsRepo = $optionsRepo;
  }
  
  public function blogInfoRequest() {
    $options = $this->optionsRepo->fetchAll(array('blogTitle', 'blogDescription'));
    $categories = $this->categoryRepo->fetchAll();
    
    $categoriesDto = array();
    
    foreach ($categories as $c) {
      $categoriesDto[] = new CategoryDTO(
        $c->id,
        $c->name
      );
    }
        
    return new BlogInfoDTO(
      $options['blogTitle']->value,
      $options['blogDescription']->value,
      $categoriesDto
    );
  }
  
  private function getIfSet($params, $var, $def = null) {
    return isset($params[$var]) ? $params[$var] : $def;
  }
}
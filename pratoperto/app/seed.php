<?php
function catalog_seed(): array {
    $restaurants = [
        ['id'=>'10000000-0000-4000-8000-000000000001','slug'=>'casa-do-pao','name'=>'Casa do Pão','category'=>'Hambúrgueres','kind'=>'burger','tag'=>'Feito na chapa','description'=>'Pão macio, ingredientes frescos e combinações que dão vontade de repetir.','fee'=>590,'minimum'=>2000,'eta_min'=>25,'eta_max'=>40,'is_open'=>1,'color'=>'#fae5c5'],
        ['id'=>'10000000-0000-4000-8000-000000000002','slug'=>'forno-do-bairro','name'=>'Forno do Bairro','category'=>'Pizza','kind'=>'pizza','tag'=>'Saiu do forno','description'=>'Massa de fermentação lenta e coberturas generosas para dividir a mesa.','fee'=>390,'minimum'=>3500,'eta_min'=>35,'eta_max'=>50,'is_open'=>1,'color'=>'#f5ddd1'],
        ['id'=>'10000000-0000-4000-8000-000000000003','slug'=>'verde-e-grao','name'=>'Verde & Grão','category'=>'Saudável','kind'=>'bowl','tag'=>'Seu almoço leve','description'=>'Bowl colorido, legumes de estação e comida que combina com o seu dia.','fee'=>0,'minimum'=>1800,'eta_min'=>20,'eta_max'=>35,'is_open'=>1,'color'=>'#e5eed3'],
        ['id'=>'10000000-0000-4000-8000-000000000004','slug'=>'nori-cozinha','name'=>'Nori Cozinha','category'=>'Japonesa','kind'=>'sushi','tag'=>'Uma pausa diferente','description'=>'Combinados, temakis e bowls montados com cuidado, na hora do pedido.','fee'=>690,'minimum'=>3000,'eta_min'=>30,'eta_max'=>45,'is_open'=>1,'color'=>'#dce9e9'],
        ['id'=>'10000000-0000-4000-8000-000000000005','slug'=>'cantina-da-praca','name'=>'Cantina da Praça','category'=>'Massas','kind'=>'pasta','tag'=>'Comida de encontro','description'=>'Receitas de massa e molho de tomate para um almoço com cara de casa.','fee'=>490,'minimum'=>2500,'eta_min'=>30,'eta_max'=>45,'is_open'=>0,'color'=>'#eadfd0'],
        ['id'=>'10000000-0000-4000-8000-000000000006','slug'=>'doce-intervalo','name'=>'Doce Intervalo','category'=>'Doces','kind'=>'cake','tag'=>'A pausa que faltava','description'=>'Brownies, bolos e café para dar um gosto melhor ao seu intervalo.','fee'=>290,'minimum'=>1200,'eta_min'=>15,'eta_max'=>30,'is_open'=>1,'color'=>'#efdfdc'],
    ];
    $menus=[
        [['Burger da casa','Pão brioche, carne, queijo, alface e molho da casa.',2590,'burger'],['Duplo da chapa','Duas carnes, queijo e cebola caramelizada.',3390,'burger'],['Batata crocante','Batatas douradas com um toque de ervas.',1390,'fries'],['Limonada fresca','Limão, água e gelo. 350 ml.',790,'drink']],
        [['Margherita de forno','Tomate, muçarela e folhas de manjericão.',4590,'pizza'],['Pizza de cogumelos','Cogumelos, queijo e um toque de azeite.',4990,'pizza'],['Pizza pepperoni','Pepperoni e queijo sobre nossa massa da casa.',5290,'pizza'],['Limonada fresca','Limão, água e gelo. 350 ml.',790,'drink']],
        [['Bowl da estação','Arroz, grão-de-bico, abacate e legumes.',2890,'bowl'],['Bowl de frango','Frango grelhado, arroz e vegetais coloridos.',3190,'bowl'],['Salada de grãos','Folhas, grãos e molho de limão.',2390,'bowl'],['Suco de laranja','Laranja espremida, 350 ml.',990,'drink']],
        [['Combinado Nori','Uma seleção de 12 peças de sushi e maki.',4990,'sushi'],['Temaki da cozinha','Alga, arroz, salmão e cebolinha.',2490,'sushi'],['Bowl oriental','Arroz, peixe e legumes em um bowl.',3590,'bowl'],['Maki de pepino','Oito peças de maki com pepino.',2190,'sushi']],
        [['Massa ao pomodoro','Massa envolvida em molho de tomate.',3290,'pasta'],['Pasta de cogumelos','Cogumelos e molho cremoso.',3690,'pasta'],['Massa ao pesto','Manjericão, azeite e queijo.',3590,'pasta'],['Limonada fresca','Limão, água e gelo. 350 ml.',790,'drink']],
        [['Brownie da casa','Chocolate, casquinha fina e centro macio.',1290,'cake'],['Fatia de chocolate','Bolo de chocolate com cobertura.',1590,'cake'],['Café com leite','Café e leite vaporizado, 250 ml.',890,'coffee'],['Combo da pausa','Brownie e café com leite.',1990,'cake']]
    ];
    $products=[];
    foreach($restaurants as $i=>$restaurant) {
        foreach($menus[$i] as $j=>$menu) {
            $number=$i*4+$j+1;
            $products[]=['id'=>sprintf('20000000-0000-4000-8000-%012d',$number),'restaurant_id'=>$restaurant['id'],'name'=>$menu[0],'description'=>$menu[1],'price'=>$menu[2],'image'=>$menu[3],'group_name'=>$j===3 && $i!==3 ? 'Para acompanhar' : 'Da casa','available'=>($i===0 && $j===2) ? 0 : 1,'options'=>[['name'=>'Porção padrão','extra'=>0],['name'=>'Porção maior','extra'=>600]]];
        }
    }
    return [$restaurants,$products];
}

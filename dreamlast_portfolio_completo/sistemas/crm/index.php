<?php
require_once __DIR__.'/includes/auth.php';
$module=$_GET['module']??'dashboard';
$allowed=['dashboard','clientes','leads','oportunidades','tarefas','projetos','tickets','financeiro','agenda','relatorios','usuarios','auditoria','configuracoes'];
if(!in_array($module,$allowed,true)) $module='dashboard';
if(!can($module) && !in_array($module,['usuarios','auditoria','configuracoes'],true)) $module='dashboard';
if(in_array($module,['usuarios','auditoria','configuracoes'],true) && ($_SESSION['user']['role']??'')!=='Administrador') $module='dashboard';

$modules=[
'clientes'=>['title'=>'Clientes','table'=>'clients','icon'=>'◫','search'=>['name','email','phone','segment'],'fields'=>[
 ['name'=>'type','label'=>'Tipo','type'=>'select','options'=>['Empresa','Pessoa']],['name'=>'name','label'=>'Nome / Razão social','required'=>1],['name'=>'document','label'=>'CPF/CNPJ'],['name'=>'email','label'=>'E-mail','type'=>'email'],['name'=>'phone','label'=>'Telefone'],['name'=>'segment','label'=>'Segmento'],['name'=>'city','label'=>'Cidade'],['name'=>'state','label'=>'UF'],['name'=>'status','label'=>'Status','type'=>'select','options'=>['Ativo','Prospect','Inativo']],['name'=>'notes','label'=>'Observações','type'=>'textarea']]],
'leads'=>['title'=>'Leads','table'=>'leads','icon'=>'◎','search'=>['name','company','email','phone','source'],'fields'=>[
 ['name'=>'name','label'=>'Nome','required'=>1],['name'=>'company','label'=>'Empresa'],['name'=>'email','label'=>'E-mail','type'=>'email'],['name'=>'phone','label'=>'Telefone'],['name'=>'source','label'=>'Origem'],['name'=>'status','label'=>'Status','type'=>'select','options'=>['Novo','Contato','Qualificado','Descartado','Convertido']],['name'=>'score','label'=>'Score','type'=>'number'],['name'=>'next_contact','label'=>'Próximo contato','type'=>'date'],['name'=>'notes','label'=>'Observações','type'=>'textarea']]],
'oportunidades'=>['title'=>'Oportunidades','table'=>'opportunities','icon'=>'◇','search'=>['title','stage'],'fields'=>[
 ['name'=>'title','label'=>'Oportunidade','required'=>1],['name'=>'client_id','label'=>'Cliente','type'=>'client'],['name'=>'value','label'=>'Valor','type'=>'number','step'=>'0.01'],['name'=>'stage','label'=>'Etapa','type'=>'select','options'=>['Prospecção','Qualificação','Proposta','Negociação','Ganho','Perdido']],['name'=>'probability','label'=>'Probabilidade %','type'=>'number'],['name'=>'expected_close','label'=>'Previsão de fechamento','type'=>'date'],['name'=>'notes','label'=>'Observações','type'=>'textarea']]],
'tarefas'=>['title'=>'Tarefas','table'=>'tasks','icon'=>'✓','search'=>['title','status','priority'],'fields'=>[
 ['name'=>'title','label'=>'Tarefa','required'=>1],['name'=>'description','label'=>'Descrição','type'=>'textarea'],['name'=>'due_date','label'=>'Prazo','type'=>'datetime-local'],['name'=>'priority','label'=>'Prioridade','type'=>'select','options'=>['Baixa','Média','Alta','Urgente']],['name'=>'status','label'=>'Status','type'=>'select','options'=>['Pendente','Em andamento','Concluída','Cancelada']]]],
'projetos'=>['title'=>'Projetos','table'=>'projects','icon'=>'▣','search'=>['name','status'],'fields'=>[
 ['name'=>'name','label'=>'Projeto','required'=>1],['name'=>'client_id','label'=>'Cliente','type'=>'client'],['name'=>'status','label'=>'Status','type'=>'select','options'=>['Planejamento','Em andamento','Pausado','Concluído','Cancelado']],['name'=>'progress','label'=>'Progresso %','type'=>'number'],['name'=>'budget','label'=>'Orçamento','type'=>'number','step'=>'0.01'],['name'=>'start_date','label'=>'Início','type'=>'date'],['name'=>'end_date','label'=>'Fim','type'=>'date'],['name'=>'description','label'=>'Descrição','type'=>'textarea']]],
'tickets'=>['title'=>'Atendimento','table'=>'tickets','icon'=>'◉','search'=>['subject','category','status'],'fields'=>[
 ['name'=>'subject','label'=>'Assunto','required'=>1],['name'=>'client_id','label'=>'Cliente','type'=>'client'],['name'=>'category','label'=>'Categoria'],['name'=>'priority','label'=>'Prioridade','type'=>'select','options'=>['Baixa','Média','Alta','Crítica']],['name'=>'status','label'=>'Status','type'=>'select','options'=>['Aberto','Em atendimento','Aguardando cliente','Resolvido','Fechado']],['name'=>'description','label'=>'Descrição','type'=>'textarea']]],
'financeiro'=>['title'=>'Financeiro','table'=>'finance','icon'=>'$','search'=>['description','category','status'],'fields'=>[
 ['name'=>'description','label'=>'Descrição','required'=>1],['name'=>'type','label'=>'Tipo','type'=>'select','options'=>['Receita','Despesa']],['name'=>'category','label'=>'Categoria'],['name'=>'amount','label'=>'Valor','type'=>'number','step'=>'0.01'],['name'=>'due_date','label'=>'Vencimento','type'=>'date'],['name'=>'paid_date','label'=>'Pagamento','type'=>'date'],['name'=>'status','label'=>'Status','type'=>'select','options'=>['Pendente','Pago','Atrasado','Cancelado']],['name'=>'client_id','label'=>'Cliente','type'=>'client']]],
];

if(isset($modules[$module])){
 $cfg=$modules[$module];
 if($_SERVER['REQUEST_METHOD']==='POST'){
  csrf_check(); $action=$_POST['action']??'save';
  if($action==='delete'){
   $id=(int)($_POST['id']??0); $pdo->prepare("DELETE FROM {$cfg['table']} WHERE id=?")->execute([$id]);
   $pdo->prepare('INSERT INTO audit_logs(user_id,action,details,ip_address) VALUES(?,?,?,?)')->execute([$_SESSION['user']['id'],'DELETE',"{$cfg['title']} #$id",$_SERVER['REMOTE_ADDR']??'']);
   flash('success','Registro excluído com sucesso.'); redirect("index.php?module=$module");
  }
  $cols=[];$vals=[];
  foreach($cfg['fields'] as $f){$cols[]=$f['name'];$v=$_POST[$f['name']]??null;if($v==='')$v=null;$vals[]=$v;}
  $id=(int)($_POST['id']??0);
  if($id){$set=implode(',',array_map(fn($c)=>"$c=?",$cols));$vals[]=$id;$pdo->prepare("UPDATE {$cfg['table']} SET $set WHERE id=?")->execute($vals);$act='UPDATE';}
  else{$ph=implode(',',array_fill(0,count($cols),'?'));$pdo->prepare("INSERT INTO {$cfg['table']} (".implode(',',$cols).") VALUES ($ph)")->execute($vals);$id=(int)$pdo->lastInsertId();$act='CREATE';}
  $pdo->prepare('INSERT INTO audit_logs(user_id,action,details,ip_address) VALUES(?,?,?,?)')->execute([$_SESSION['user']['id'],$act,"{$cfg['title']} #$id",$_SERVER['REMOTE_ADDR']??'']);
  flash('success','Registro salvo com sucesso.'); redirect("index.php?module=$module");
 }
}

$flash=getFlash();
include __DIR__.'/includes/header.php';
if(isset($modules[$module])) { $cfg=$modules[$module]; include __DIR__.'/pages/crud.php'; }
else { $file=__DIR__."/pages/$module.php"; if(file_exists($file)) include $file; else include __DIR__.'/pages/dashboard.php'; }
include __DIR__.'/includes/footer.php';

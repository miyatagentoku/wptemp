<?php
/* ----------------------------------------------------
*
* DB関連処理
*
-------------------------------------------------------*/

/*
	DBを使用する際の大元のクラス
	コネクションおよびSQLの発行などすべてここで管理する
 */
class BaseDAO {

	private $con;

	/* コンストラクタ */
	public function __construct(){ }

	/* コネクションを取得します */
	protected function setConnection( $dbhost, $dbname, $dbuser, $dbpass ){

		$flg = true;
		$err = null;

		try{
			// コネクションを取得
			$this->con = new PDO('mysql:host='.$dbhost.';dbname='.$dbname.';charset=utf8', $dbuser, $dbpass);
			// 文字コードを設定
			$this->con->query('SET NAMES utf8');
			// エラー出力を設定
			$this->con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
			// トランザクションの開始
			$this->con->beginTransaction();

		} catch(Exception $e){
			$flg = false;
			$err = $e;
		}

		return array('result' => $flg, 'err' => $err);
	}

	/* コネクションを切断します */
	protected function closeConnection(){

		$flg = true;
		$err = null;

		try{
			$this->con = null;
		} catch(Exception $e){
			$flg = false;
			$err = $e;
		}

		return array('result' => $flg, 'err' => $err);
	}

	/* トランザクションをコミットします */
	protected function commit(){

		$flg = true;
		$err = null;

		try{

			$this->con->commit();

		} catch(Exception $e){
			$flg = false;
			$err = $e;
		}

		return array('result' => $flg, 'err' =>$err);
	}

	/* トランザクションをロールバックします */
	protected function rollback(){

		$flg = true;
		$err = null;

		try{

			$this->con->rollBack();

		}catch(Exception $e){
			$flg = false;
			$err = $e;
		}

		return array('result' => $flg, 'err' => $err);
	}

	/*
		同期処理を行うために引数で渡されたテーブルにロックをかけます
		LOCK TABLES TABLE_NAME WRITE,TABLE_NAME_2 WRITE ....
		処理に成功した場合はtrueを返し、失敗した場合は
		falseとExceptionオブジェクトを返します。
	*/
	protected function tableLock( $tables ){

		$flg = true;
		$err = null;

		try{
			$sql = 'LOCK TABLES '.join(' WRITE,',$tables).' WRITE';

			$stmt = $this->con->prepare($sql);

			$stmt->execute();

		} catch(Exception $e){
			$flg = false;
			$err = $e;
		}

		return array('result' => $flg, 'err' => $err);
	}

	/* テーブルのロックを解除します */
	protected function tableUnLock(){

		$flg = true;
		$err = null;

		try{
			$sql = 'UNLOCK TABLES';

			$stmt = $this->con->prepare($sql);

			$stmt->execute();

		}catch(Exception $e){
			$flg = false;
			$err = $e;
		}

		return array('result' => $flg, 'err' => $err);
	}

	/*
		SQL文を発行する
		$query : String SQL
		$array : [ array() ]
				array(
					'placeholder' : プレースホルダーキー
					'value' : プレースホルダーに指定する値
					'paramtype' : プレースホルダーに指定する値のデータ型
				)
	*/
	protected function run( $query, $array=null ) {

		$flg = true;
		$err = null;
		$stmt = null;

		try{

			$stmt = $this->con->prepare($query);

			// プレースホルダーを指定する場合
			if( $array != null ) {
				$i = 0;
				$len = count($array);
				while ( $i < $len) {
					$stmt->bindValue( $array[$i]['placeholder'], $array[$i]['value'], $array[$i]['paramtype'] );
					$i++;
				}
			}

			$stmt->execute();

		} catch(Exception $e){
			$flg = false;
			$err = $e;
		}

		return array('result' => $flg, 'data' => $stmt, 'err' => $err);
	}

}

/* ----------------------------------------------------
*
* Database Access Object
*
-------------------------------------------------------*/

/*
	example
*/
class SearchDAO extends BaseDAO {
	private $dbhost;
	private $dbname;
	private $dbuser;
	private $dbpass;

	function __construct( $dbhost, $dbname, $dbuser, $dbpass) {
		$this->dbhost = $dbhost;
		$this->dbname = $dbname;
		$this->dbuser = $dbuser;
		$this->dbpass = $dbpass;
	}

	/*
		投稿タイプを指定して、その投稿タイプの記事の登録数を取得する
		result : 実行結果の真偽値(成功：true,失敗：false)
	*/
	public function getPostCount(  ) {
		$arr = array(
			'result'=> true,
			'data'=>[]
		);

		try {
			parent::setConnection($this->dbhost,$this->dbname,$this->dbuser,$this->dbpass);

			$query = "SELECT COUNT(ID) AS count FROM wp_posts WHERE post_type = 'search' AND post_status = 'publish';";

			$resultset = parent::run( $query );

			if ( $resultset['result'] == true ) {

				$result = $resultset['data']->fetch( PDO::FETCH_ASSOC );
				$arr['data'] = $result['count'];

			} else {
				$arr = array(
					'result'=>false
				);
			}

		} catch (Exception $e) {
			$arr = array(
				'result'=>false
			);
		} finally {

			parent::closeConnection();
		}

		return $arr;
	}

	/*
		投稿タイプを指定して、その投稿タイプの記事の一覧を取得する
		result : 実行結果の真偽値(成功：true,失敗：false)
	*/
	public function getPostsByFreeword( $text ) {
		$arr = array(
			'result'=> true,
			'data'=>[]
		);

		try {
			parent::setConnection($this->dbhost,$this->dbname,$this->dbuser,$this->dbpass);

			$query = "SELECT DISTINCT main.ID FROM wp_posts AS main LEFT JOIN (SELECT a.ID,b.meta_value,b.name FROM wp_posts AS a INNER JOIN ( SELECT p.post_id,p.meta_value,ttt.name FROM wp_postmeta as p INNER JOIN ( SELECT wp_term_relationships.object_id,tt.name FROM wp_term_relationships INNER JOIN (SELECT wp_term_taxonomy.term_taxonomy_id,wp_terms.name FROM wp_term_taxonomy INNER JOIN wp_terms ON wp_term_taxonomy.term_id = wp_terms.term_id ) AS tt ON wp_term_relationships.term_taxonomy_id = tt.term_taxonomy_id ) AS ttt ON p.post_id = ttt.object_id WHERE p.meta_key NOT LIKE '\_%' ESCAPE '\\' ) AS b ON a.ID = b.post_id) AS subquery ON main.ID = subquery.ID WHERE main.post_type = 'search' AND (main.post_title LIKE :meta_value OR subquery.meta_value LIKE :meta_value OR subquery.name LIKE :meta_value) AND main.post_status = 'publish'";

			$array = [ array( 'placeholder' => ':meta_value', 'value' => '%'.$text.'%', 'paramtype' => PDO::PARAM_STR ) ];

			$resultset = parent::run( $query, $array );

			if ( $resultset['result'] == true ) {

				while ($result = $resultset['data']->fetch( PDO::FETCH_ASSOC ) ) {
					array_push( $arr['data'], $result['ID']);
				}

			} else {
				$arr = array(
					'result'=>false
				);
			}

		} catch (Exception $e) {
			$arr = array(
				'result'=>false
			);
		} finally {

			parent::closeConnection();
		}

		return $arr;
	}

}

/*
	CommonDAO
	基本的な取得処理
*/
class CommonDAO extends BaseDAO {

	private $dbhost;
	private $dbname;
	private $dbuser;
	private $dbpass;

	function __construct( $dbhost, $dbname, $dbuser, $dbpass) {
		$this->dbhost = $dbhost;
		$this->dbname = $dbname;
		$this->dbuser = $dbuser;
		$this->dbpass = $dbpass;
	}

	/*
		投稿タイプを指定して、その投稿タイプの記事の一覧を取得する
	*/
	public function getPosts( $post_type ) {
		$arr = array(
			'result'=> true,
			'data'=>[]
		);

		try {
			parent::setConnection($this->dbhost,$this->dbname,$this->dbuser,$this->dbpass);

			$query = "SELECT wp_posts.post_title AS post_title,wp_posts.post_date AS post_date, wp_posts.post_type AS post_type ,wp_posts.post_name AS post_name,postmeta_and_termtaxonomy.meta_key AS meta_key,postmeta_and_termtaxonomy.meta_value AS meta_value,postmeta_and_termtaxonomy.taxonomy AS taxonomy,postmeta_and_termtaxonomy.name AS term_name,postmeta_and_termtaxonomy.slug AS term_slug,postmeta_and_termtaxonomy.object_id AS post_id, postmeta_and_termtaxonomy.imagepath AS imagepath FROM wp_posts INNER JOIN ( SELECT postmeta_and_imagepath.meta_key,postmeta_and_imagepath.meta_value,postmeta_and_imagepath.imagepath,term_taxonomy_relation.object_id,term_taxonomy_relation.taxonomy,term_taxonomy_relation.description,term_taxonomy_relation.name,term_taxonomy_relation.slug FROM ( SELECT wp_postmeta.meta_id,wp_postmeta.post_id,wp_postmeta.meta_key,wp_postmeta.meta_value,wp_posts.guid AS imagepath FROM wp_postmeta LEFT JOIN wp_posts ON wp_postmeta.meta_value = wp_posts.id ) AS postmeta_and_imagepath INNER JOIN ( SELECT wp_term_relationships.object_id,termtaxonomy.term_taxonomy_id,termtaxonomy.taxonomy,termtaxonomy.description,termtaxonomy.name,termtaxonomy.slug FROM wp_term_relationships INNER JOIN ( SELECT wp_term_taxonomy.term_taxonomy_id, wp_term_taxonomy.taxonomy, wp_term_taxonomy.description, wp_terms.name, wp_terms.slug FROM wp_term_taxonomy INNER JOIN wp_terms ON wp_term_taxonomy.term_id = wp_terms.term_id) AS termtaxonomy ON wp_term_relationships.term_taxonomy_id = termtaxonomy.term_taxonomy_id ) AS term_taxonomy_relation ON postmeta_and_imagepath.post_id = term_taxonomy_relation.object_id WHERE postmeta_and_imagepath.meta_key NOT LIKE '¥_%' ESCAPE '¥' OR postmeta_and_imagepath.meta_key = '_thumbnail_id' ) AS postmeta_and_termtaxonomy ON wp_posts.id = postmeta_and_termtaxonomy.object_id WHERE wp_posts.post_status = 'publish' AND wp_posts.post_type = :post_type ORDER BY wp_posts.post_date DESC, postmeta_and_termtaxonomy.description ASC;";

			$array = [ array( 'placeholder' => ':post_type', 'value' => $post_type, 'paramtype' => PDO::PARAM_STR ) ];

			$resultset = parent::run( $query, $array );

			if ( $resultset['result'] == true ) {

				$row = null;

				while ($result = $resultset['data']->fetch( PDO::FETCH_ASSOC ) ) {

					if( $row == $result['post_id'] ) {
						// wp_postmeta のレコードを全て取得
						$arr['data']['row_'.$row][ $result['meta_key'] ] = $result['meta_value'];
					} else {
						$row = $result['post_id'];
						$arr['data']['row_'.$row]['title'] = $result['post_title'];
						$arr['data']['row_'.$row]['post_date'] = $result['post_date'];
						$arr['data']['row_'.$row]['post_type'] = $result['post_type'];
						$arr['data']['row_'.$row]['post_name'] = $result['post_name'];
						$arr['data']['row_'.$row]['taxonomy'] = $result['taxonomy'];
						$arr['data']['row_'.$row]['termname'] = $result['term_name'];
						// wp_postmeta のレコード
						$arr['data']['row_'.$row][ $result['meta_key'] ] = $result['meta_value'];
					}
					// サムネイル画像またはカスタムフィールドタイプ【画像】を使用している場合（ $post_type.'_image' はあくまで例 ）
					if( $result['meta_key'] === $post_type.'_image' || $result['meta_key'] === '_thumbnail_id' ) {
						$arr['data']['row_'.$row][ $result['meta_key'] ] = $result['imagepath'];
					}
				}

			} else {
				$arr = array(
					'result'=>false
				);
			}

		} catch (Exception $e) {
			$arr = array(
				'result'=>false
			);
		} finally {

			parent::closeConnection();
		}

		return $arr;
	}

	public function getPostCountByTermAndPostType( $term, $post_type ) {
		$arr = array(
			'result' => true,
			'data' => []
		);

		try {
			parent::setConnection($this->dbhost,$this->dbname,$this->dbuser,$this->dbpass);

			$query = "SELECT COUNT(wp_posts.ID) AS count FROM wp_posts INNER JOIN (SELECT wp_term_relationships.object_id FROM wp_term_relationships INNER JOIN ( SELECT wp_term_taxonomy.term_taxonomy_id FROM wp_term_taxonomy INNER JOIN (SELECT term_id FROM wp_terms WHERE slug = :term) AS wp_terms ON wp_term_taxonomy.term_id = wp_terms.term_id ) AS wp_term_taxonomy ON wp_term_relationships.term_taxonomy_id = wp_term_taxonomy.term_taxonomy_id) AS wp_term_relationships ON wp_posts.ID = wp_term_relationships.object_id WHERE wp_posts.post_status = 'publish' AND wp_posts.post_type = :post_type";

			$array = [
				array('placeholder' => ':post_type', 'value' => $post_type, 'paramtype' => PDO::PARAM_STR),
				array('placeholder' => ':term', 'value' => $term, 'paramtype' => PDO::PARAM_STR)
			];

			$resultset = parent::run( $query, $array );

			if ( $resultset['result'] == true ) {

				$result = $resultset['data']->fetch( PDO::FETCH_ASSOC );
				$arr['data'] = $result['count'];

			} else {
				$arr = array(
					'result'=>false
				);
			}

		} catch (Exception $e) {
			$arr = array(
				'result'=>false
			);
		} finally {

			parent::closeConnection();
		}

		return $arr;

	}

	/*
		投稿タイプとタームを指定して、その投稿タイプのうち指定されたタームを持つ記事を取得する
	*/
	public function getPostsByTerm( $post_type, $term ) {
		$arr = array(
			'result'=> true,
			'data'=>[]
		);

		try {
			parent::setConnection($this->dbhost,$this->dbname,$this->dbuser,$this->dbpass);

			$query = "SELECT wp_posts.post_title AS post_title,wp_posts.post_date AS post_date, wp_posts.post_type AS post_type ,wp_posts.post_name AS post_name,postmeta_and_termtaxonomy.meta_key AS meta_key,postmeta_and_termtaxonomy.meta_value AS meta_value,postmeta_and_termtaxonomy.taxonomy AS taxonomy,postmeta_and_termtaxonomy.name AS term_name,postmeta_and_termtaxonomy.slug AS term_slug,postmeta_and_termtaxonomy.object_id AS post_id, postmeta_and_termtaxonomy.imagepath AS imagepath FROM wp_posts INNER JOIN ( SELECT postmeta_and_imagepath.meta_key,postmeta_and_imagepath.meta_value,postmeta_and_imagepath.imagepath,term_taxonomy_relation.object_id,term_taxonomy_relation.taxonomy,term_taxonomy_relation.description,term_taxonomy_relation.name,term_taxonomy_relation.slug FROM ( SELECT wp_postmeta.meta_id,wp_postmeta.post_id,wp_postmeta.meta_key,wp_postmeta.meta_value,wp_posts.guid AS imagepath FROM wp_postmeta LEFT JOIN wp_posts ON wp_postmeta.meta_value = wp_posts.id ) AS postmeta_and_imagepath INNER JOIN ( SELECT wp_term_relationships.object_id,termtaxonomy.term_taxonomy_id,termtaxonomy.taxonomy,termtaxonomy.description,termtaxonomy.name,termtaxonomy.slug FROM wp_term_relationships INNER JOIN ( SELECT wp_term_taxonomy.term_taxonomy_id, wp_term_taxonomy.taxonomy, wp_term_taxonomy.description, wp_terms.name, wp_terms.slug FROM wp_term_taxonomy INNER JOIN wp_terms ON wp_term_taxonomy.term_id = wp_terms.term_id) AS termtaxonomy ON wp_term_relationships.term_taxonomy_id = termtaxonomy.term_taxonomy_id ) AS term_taxonomy_relation ON postmeta_and_imagepath.post_id = term_taxonomy_relation.object_id WHERE postmeta_and_imagepath.meta_key NOT LIKE '¥_%' ESCAPE '¥' OR postmeta_and_imagepath.meta_key = '_thumbnail_id' ) AS postmeta_and_termtaxonomy ON wp_posts.id = postmeta_and_termtaxonomy.object_id WHERE wp_posts.post_status = 'publish' AND wp_posts.post_type = :post_type AND postmeta_and_termtaxonomy.slug = :term ORDER BY wp_posts.post_date DESC, postmeta_and_termtaxonomy.description ASC;";

			$array = [
				array('placeholder' => ':post_type', 'value' => $post_type, 'paramtype' => PDO::PARAM_STR),
				array('placeholder' => ':term', 'value' => $term, 'paramtype' => PDO::PARAM_STR)
			];

			$resultset = parent::run( $query, $array );

			if ( $resultset['result'] == true ) {

				$row = null;

				while ($result = $resultset['data']->fetch( PDO::FETCH_ASSOC ) ) {

					if( $row == $result['post_id'] ) {
						// wp_postmeta のレコードを全て取得
						$arr['data']['row_'.$row][ $result['meta_key'] ] = $result['meta_value'];
					} else {
						$row = $result['post_id'];
						$arr['data']['row_'.$row]['title'] = $result['post_title'];
						$arr['data']['row_'.$row]['post_date'] = $result['post_date'];
						$arr['data']['row_'.$row]['post_type'] = $result['post_type'];
						$arr['data']['row_'.$row]['post_name'] = $result['post_name'];
						$arr['data']['row_'.$row]['taxonomy'] = $result['taxonomy'];
						$arr['data']['row_'.$row]['termname'] = $result['term_name'];
						// wp_postmeta のレコード
						$arr['data']['row_'.$row][ $result['meta_key'] ] = $result['meta_value'];
					}
					// サムネイル画像またはカスタムフィールドタイプ【画像】を使用している場合（ $post_type.'_image' はあくまで例 ）
					if( $result['meta_key'] === 'products_image' || $result['meta_key'] === '_thumbnail_id' ) {
						$arr['data']['row_'.$row][ $result['meta_key'] ] = $result['imagepath'];
					}
				}

			} else {
				$arr = array(
					'result'=>false
				);
			}

		} catch (Exception $e) {
			$arr = array(
				'result'=>false
			);
		} finally {

			parent::closeConnection();
		}

		return $arr;
	}

	/*
		投稿タイプを指定して、その投稿タイプに紐づくタームの一覧を取得する
	*/
	public function getTerms( $post_type ) {

		$arr = array(
			'result'=> true,
			'data'=>[]
		);

		try {
			parent::setConnection($this->dbhost,$this->dbname,$this->dbuser,$this->dbpass);

			$query = "SELECT DISTINCT a.slug FROM wp_posts INNER JOIN (SELECT * FROM wp_term_relationships INNER JOIN (SELECT wp_terms.term_id,wp_terms.slug,wp_term_taxonomy.description FROM wp_terms INNER JOIN wp_term_taxonomy ON wp_terms.term_id = wp_term_taxonomy.term_id ) AS termtaxonomy ON wp_term_relationships.term_taxonomy_id =termtaxonomy.term_id ) AS a ON wp_posts.ID = a.object_id WHERE post_type = :post_type ORDER BY a.description ASC";

			$array = [ array( 'placeholder' => ':post_type', 'value' => $post_type, 'paramtype' => PDO::PARAM_STR ) ];

			$resultset = parent::run( $query, $array );

			if ( $resultset['result'] == true ) {

				while ($result = $resultset['data']->fetch( PDO::FETCH_ASSOC ) ) {
					array_push( $arr['data'], $result['slug']);
				}

			} else {
				$arr = array(
					'result'=>false
				);
			}

		} catch (Exception $e) {
			$arr = array(
				'result'=>false
			);
		} finally {

			parent::closeConnection();
		}

		return $arr;
	}
}
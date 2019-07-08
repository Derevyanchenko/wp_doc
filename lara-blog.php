php artisan make:model Tag -m
php artisan make:controller Admin\PostsController --resourse

mysql -u root
create database blog;

Планирование:

- Категория
- Тег 
- Пользователь
- Коментарий
- Подписка
- Пост


1.) Post

	поля в табл
		- id
		- title
		- slug
		- content
		- data
		- image
		- desc
		- cat_id
		- user_id
		- status
		- views
		- is_featured
		- created_at
		- updated_at

	Связь с тегами через табл: - post_tags

	add()
	edit
	remove
	delete
	upl_image
	remove_image
	setCat
	setTegs
	toggle_status
	toggle_Feature

///////////	

2.) Category

	поля в табл
		- id
		- title
		- slug

	Связь с тегами через табл: - posts

///////////	

3.) user

	поля в табл
		- id
		- name
		- email
		- password
		- is_Admin
		- status
		- avatar
		- created_at
		- updated_at

	Связь с тегами через табл: - posts, comments

	add()
	edit
	remove
	delete
	upl_avatar
	remove_avatar
	toggle_admin
	toggle_status

///////////	


4.) comment

	поля в табл
		- id
		- text
		- user_id
		- post_id
		- status
		- created_at
		- updated_at

	Связь с тегами через табл: - posts, author, toggleStatus

///////////	



5.) Subscription

	поля в табл
		- id
		- email
		- token
		- created_at
		- updated_at

	add()
	remove()

///////////	



-----------------------------------------------------------------------------

Таблицы:

	- posts
	- users
	- comments
	- categories
	- tags
	- post_tags
	- subscriptions



Routes:
 
	- posts

	GET admin/posts PostsController@index
	GET admin/posts/create PostsController@create
	Post admin/posts PostsController@store
	GET admin/posts/{id}/edit PostsController@edit
	PUT admin/posts/{id}/ PostsController@edit
	DELETE admin/posts/{id}/delete PostsController@delete


===================================================================================

Миграции:

 	- сategory

 		$table->string("title");
 		$table->string("slug");

 	- tags

 		$table->string("title");
 		$table->string("slug");

 	- comment

 		$table->text("text");
 		$table->integer("user_id");
 		$table->integer("post_id");
 		$table->integer("status")->default(0);

 	- user

 		$table->string("name");
 		$table->string("email");
 		$table->string("password");
 		$table->integer("is_admin")->default(0);
 		$table->integer("status")->default(0);
 		$table->rememberToken();


 	- post

 		$table->string("title");
 		$table->string("slug");
 		$table->text("contnent");
 		$table->integer("cat_id")->nullable();
 		$table->integer("user_id")->nullable();
 		$table->integer("status")->default(0);
 		$table->integer("views")->default(0);
 		$table->integer("is_featured")->default(0);

 	- post_tags

 		$table->integer("post_id");
 		$table->integer("tag_id");

 	- subscriples

 		$table->string("email");
 		$table->string("token")->nullable();


===================================================================================


<?php  


Модели: 

// взаимосвязи между сущностями

	//- post.php

		use Illuminate\Support\Facades\Storage;

		protected $fillable = ["title", "content"];

		public function category()
		{
			return $this->hasOne(Category::class);
		}

		public function author()
		{
			return $this->hasOne(User::class);
			// Пост может иметь только одного автора
		}

		public function tags()
		{
			return $this->bellowToMany(
				Tag::class,
				"post_tags",
				"post_id",
				"tag_id"
			);
		}

		public static function add($fields)
		{
			$post = new static;
			$post->fill($fields);
			$post->user_id = 1;
			$post->save();

			return $post;
		}

		public function edit($fields)
		{
			$this->fill($fields);
			$this->save();
		}

		public function remove()
		{
			Storage::delte("uploads/" . $this->image);
			$this->delete();
		}

		// Загрузка картинок

		// !! для этого в config/filesystem

		/*"local" => [
			"root" => public_path("app")
		]
*/
		public function upload_image($image)
		{
			if($image == null){ return; }

			Storage::delte("uploads/" . $this->image);
			$filename = str_random(10) . "." . $image->extension();
			$image->saveAs("uploads", $fillename);
			$this->image = $filename;
			$this->save();
		}

		public function setCategory($id)
		{
			$this->category_id = $id;
			$this->save();
		}

		public function setCategory($ids)
		{
			
			$this->tags()->sync($ids);
		}

		// status
			// черновик
		public function setDraft()
		{
			$this->status = 0;
			$this->save();
		}

		public function setPublic()
		{
			$this->status = 1;
			$this->save();
		}

		public function toggleStatus($val)
		{
			if($val == null)
			{
				return $this->setDraft();
			}
			else {
				return $this->setPublic();
			}
		}

		// featured

		public function setFeatured()
		{
			 $this->is_featured = 1;
		}

		public function setStandart()
		{
			 $this->is_featured = 0;
		}

		public function toggleFeatured($val)
		{
			if( $val == null )
			{
				return $this->setStandart();
			}	
			else
			{
				return $this->setFeatured();
			}
		}

		// вывод картинки
		// пример: $post->getImage()

		public function getImage()
		{
			if($this->image == null)
			{
				return "img/no-image.png";
			}

			return "/uploads" . $this->image;
		}


	// - category.php

		public function posts()
		{
			return $this->hasMany(Post::class);
			// категория может иметь множество постов
		}

	// - tag.php

		public function posts()
		{
			return $this->bellowToMany(
				Post::class,
				"post_tags",
				"post_id",
				"tag_id"
			);
		}

	// - user.php

		public function posts()
		{
			return $this->hasMany(Post::class);
		}

		public function comments()
		{
			return $this->hasMany(Comment::class);
		}

		public static function add($fields)
		{
			$user = new static;
			$user->fill($fields);
			$user->password = bcript($fields["password"]);
			$user->save();

			return $user;
		}

		public function edit($fields)
		{
			$this->fill($fields);
			$this->password = bcript($fields["password"]);
			$this->save();
		}

		public function remove()
		{
			$this->delete();
		}

		public function upload_avatar($image)
		{
			if($image == null){ return; }

			Storage::delte("uploads/" . $this->image);
			$filename = str_random(10) . "." . $image->extension();
			$image->saveAs("uploads", $fillename);
			$this->image = $filename;
			$this->save();
		}

		// вывод картинки
		// пример: $post->getImage()

		public function getImage()
		{
			if($this->image == null)
			{
				return "img/no-user-avatar.png";
			}

			return "/uploads" . $this->image;
		}

		public function makeAdmin()
		{
			$this->is_admin = 1;
			$this->save();
		}

		public function makeStandart()
		{
			$this->is_admin = 0;
			$this->save();
		}

		public function toggleAdmin($val)
		{
			if($val == null)
			{
				return $this->setStandart();
			}	

			return $this->makeAdmin();
		}

		// аналогично функции бана, разбана и тогл



	// - comment.php


		public function post()
		{
			return $this->hasOne(Post::class);
		}

		public function author()
		{
			return $this->hasOne(User::class);
		}

		// аналогично функции бана, разбана и тогл



	// - subscription.php

		public static function add($email)
		{
			$sub = new static;
			$sub->email = $email;
			$sub->token = str_random(100);
			$sub->save();

			return $sub;
		}

		public function remove()
		{
			$this->delete();
		}

?>


<!-- админка admin lte -->

в resourses/assets/admin:
	- в нее переносим содержимое папки assets admin'a lte

!! dashboard.html - главная стр админки

Подключение стилей через вебпак:

	in webpack.mix.js:

		// так же пробежаться по всем страницам и подключить недостающие стили и скрипты

		mix.styles([
			"resourses/assets/admin/bootstrap/css/bootstrap.min.css",
			// other styles
		], "public/css/admin.css" );

		mix.scripts([
			"resourses/assets/admin/bootstrap/js/bootstrap.min.js",
			"resourses/assets/admin/dist/js/scripts.js", // в него кидаем остальные вызовы скриптов на страницах ( например select2(); )
			// other styles
		], "public/js/admin.js" );

		mix.copy("resourses/assets/admin/bootstrap/fonts", "public/fonts");
		// также фонтасом и картинки копируем

	// npm run dev

// роуты админки

<?php  

	Route::group(["prefix" => "admin", "namespace" => "Admin"], function(){
		Route::get("/", "DashboardController@index");
		Route::resourse("/categories", "CategoriesController");
		Route::resourse("/tags", "TagsController");
		Route::resourse("/users", "UsersController");
		Route::resourse("/posts", "PostsController");
	});
	
	

?>


// контроллер категорий админки

<?php  

	use App\Category;
	
	public function index()
	{
		$categories = Category::all();
		return view("admin.categories.index", ["categories" => $categories]);
	}

	public function create()
	{
		return view("admin.categories.create");
	}

	public function store( Request $request )
	{
		Category::create($request->all());
		return view("admin.categories.create");
	}


	// post

		// view index

			{{Form::open([
				"route" => "posts.store",
				"files" => true
			])}}


?>
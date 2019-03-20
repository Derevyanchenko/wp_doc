<?php  

composer create-project --prefer-dist laravel/laravel blog

// app->http->controllers  --- Контроллеры

// app->http->свои файлы моделей

// .env  ------- Настройки и доступы к бд и хосту

	DB_DATABASE=app
	DB_USERNAME=root
	DB_PASSWORD=

// Migration example

	php artisan make:migration create_toys_table ==create-toys

	php artisan migrate

// artisan creating Controllers

	php artisan make:controller ToysController

// Вызов экшена индекс в тойсКонтроллере

	// web.php

	Route::get("toys", "ToysController@index");

	// toysController.php

	class ToysController extends Controller
	{
		public function index()
		{
			dd(1);
			return view("welcome");
		}

		public function create()
		{
			dd(1);
			return view("toys.create");
		}
	}


// работа с blade.php

	// welcome.blade.php будет нашым лейаутом




	// создаем toys->index.blade.php

		@extends("welcome")  // подключились к леауту

		@section("content")

			// html content

		@endsection


	// in welcome.blade.php

		<body>

			@yield("content") // указали место куда будет наследобаться контент других страниц

		</body>


?>



<?php

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////


// task manager project:


	php artisan make:migration create_tasks_table --create=tasks

	php artisan make:model Task

	php artisan make:controller TasksController

	// in migration дополним наш скрипт создания таблицы

		$table->increments("id");
		$table->string("title");
		$table->text("description");
		$table->timestamps();

	php artisan migrate

	// пишем роут главной

	Route::get("tasks", "TasksController@index")->name("tasks.index");

	Route::get("tasks/create", "TasksController@create")->name("tasks.create");

	// описываем Контроллер индекса

	use App\Task; // подключение модели Таск

	class TasksController extends Controller
	{
		public function index()
		{
			return view("tasks.index");
		}

		public function create()
		{
			return view("tasks.create");
		}
	}

	// вызов роута по name()

	<a href="{{ route('tasks.create') }}"></a>


	// пример формы с экшеном

	composer require "laravelcollective/html":"^5.4.0"

		/*providers*/

		Collective\Html\HtmlServiceProvider::class,

		// aliases

		'Form' => Collective\Html\FormFacade::class,
	    'Html' => Collective\Html\HtmlFacade::class,

		{!! Form::open(["route" => ["tasks.store"]]) !!}
	        // инпуты и кнопка подтверждения
	    {!! Form::close() !!}

	    // или так с атрибутами

	     {!! Form::open(["route" => ["tasks.update", $task->id], "method" => "PUT"]) !!}


    // redirect on home page

    return redirect()->route("tasks.index");


	// обработка форм


		dd($request->all());
		dd($request->only("title"));  // выбор только одной строки
	    dd($request->except("title"));	// выбор только все кроме title

	    $task = new Task;
	    $task->title = $request->get("title");  // закидываем в таблицу инфо поля title
	    $task->description = $request->get("description");  // закидываем в таблицу инфо поля description

	    $task->save(); // сохрвняем данные в базе


	// Функция в модели про массовое заполнение полей

	    class Task extends Model
		{
		    protected $fillable = ["title", "description"];
		}

	// отправка через fill

		$task = new Task;
		$task->fill($request->all());	
		
		$task->save();	

	// Задача на лету

		Task::create($request->all());



	// Валидация


		// include validation component

		use Illuminate\Foundation\Validation\ValidatesRequests;

		$this->validate($request, [
    		"title" 	  => "required",
    		"description" => "required"
    	]);


		// in errors.blade.php

		 @if($errors->any())

		    <div class="container">
		        <div class="row">
		            <div class="col-lg-10 col-lg-offset-1">
		                <div class="alert alert-danger">
		                    <ul>
		                        @foreach($errors->all() as $error)
		                            <li>{{ $error }}</li>
		                        @endforeach
		                    </ul>
		                </div>
		            </div>
		        </div>
		    </div>

		@endif

		// подключение шаблонов блейд

		@include("errors")


		// создание класса валидации

		php artisan make:request createTaskRequest

		public function rules()
	    {
	        return [
	           "title"    => "required",
	            "description" => "required"
	        ];
	    }

	    // in controller

	    use App\Http\Requests\createTaskRequest;

	    public function store(CreateTaskRequest $request)
	    {
	    	Task::create($request->all());
	    	return redirect()->route("tasks.index");
	    }

	     public function index()
	    {
	    	$tasks = Task::all();
	    	return view("tasks.index", ["tasks" => $tasks]);
	    }


	    // read single task

	    public function view($id)
	    {
	    	$task = Task::find($id);

	    	return view("tasks.view", ["task" => $task]);
	    }

	    <a href="{{ route('tasks.view', $task->id) }}" class="btn btn-primary">view</a>

	    // delete

	    public function delete($id)
	    {
	    	$tasks = Task::find($id)->delete();

	    	return redirect()->route("tasks.index");
	    }

	    {!! Form::open(["method" => "DELETE", "route" => ["tasks.delete", $task->id]]) !!}
			<button class="btn btn-danger" onclick="return confirm('are you sure?')">delete</button>
		{!! Form::close() !!}



?>

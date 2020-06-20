<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Mail;
use Validator;

// Models
use App\Models\Article;
use App\Models\Category;
use App\Models\Page;
use App\Models\Contact;
use App\Models\Config;

class Homepage extends Controller
{
    public function __construct(){ // tüm fonksiyonlarda çalışması için
        if (Config::find(1)->active==0) {
          return redirect()->to('site-bakimda')->send();
        }
        view()->share('pages', Page::where('status',1)->orderBy('order', 'ASC')->get());
        view()->share('categories', Category::where('status',1)->inRandomOrder()->get());
    }
    public function index(){
      $data['articles'] = Article::with(['getCategory'])->where('status',1)->whereHas('getCategory', function($query){
        $query->where('status',1);
      })->orderBy('created_at', 'DESC')->paginate(10);
      $data['articles']->withPath(url('sayfa'));
      return view('front.homepage', $data);
    }

    public function single($category, $slug){ // 2 url inde doğru olmasını zorunlu tutuyoruz.
      $category = Category::where('slug', $category)->first() ?? abort(403, 'Böyle bir kategori bulunamadı');
      $article = Article::where('slug', $slug)->whereCategoryId($category->id)->first()?? abort(403, 'Böyle bir yazı bulunamadı');
      $article ->increment('hit');
      $data['article'] = $article;
      return view('front.single', $data);
    }

    public function category($slug){
      $category = Category::where('slug', $slug)->first() ?? abort(403, 'Böyle bir kategori bulunamadı');
      $data['category'] = $category;
      $data['articles'] = Article::where('category_id', $category->id)->where('status',1)->orderBy('created_at', 'DESC')->paginate(1);
      return view('front.category', $data);
    }

    public function page($slug){
      $page = Page::whereSlug($slug)->first() ?? abort(403, 'Böyle bir sayfa bulunamadı');
      $data['page']=$page;
      return view('front.page', $data);
    }

    public function contact(){
      return view('front.contact');
    }

    public function contactpost(Request $request){
      $rules = [
        'name' => 'required | min:5',
        'email' => 'required | email',
        'topic' => 'required',
        'message' => 'required | min:10'
      ];
      $validate = Validator::make($request->post(), $rules);

      if ($validate->fails()) {
        return redirect()->route('contact')->withErrors($validate)->withInput();
      }

      Mail::send([],[],function($message)use($request){
        $message->from('iletisim@blogsitesi.com', 'Blog Sitesi');
        $message->to('iletisim@blogsitesi.com');
        $message->setBody(' Mesajı Gönderen: '. $request->name. '<br>
                    Mesajı Gönderen Mail: '. $request->email .' <br>
                    Mesajı Konusu: '. $request->topic .' <br>
                    Mesajı: '. $request->messsage .' <br><br>
                    Mesaj Gönderilme Tarihi: '. now() .'', 'text/html ');
        $message->subject($request->name . ' İletişimden mesaj gönderdi!');
      });

      // $contact = new Contact;
      // $contact->name = $request->name;
      // $contact->email = $request->email;
      // $contact->topic = $request->topic;
      // $contact->message = $request->message;
      // $contact->save();
      return redirect()->route('contact')->with('success', 'İletişim Mesajınız bize iletildi teşekkürler');
    }

} //

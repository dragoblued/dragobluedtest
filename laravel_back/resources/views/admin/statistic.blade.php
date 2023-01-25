@extends('layouts.admin')
@section('content')
   <div class="container-fluid">
      @if(@items)
       @foreach($items as $item)
       <div class="accordion accordion-flush" id="accordionFlushExample">
         <div class="accordion-item">
            <h2 class="accordion-header" id="flush-headingOne{{$item->id}}">
               <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne{{$item->id}}" aria-expanded="false" aria-controls="flush-collapseOne">
                  {{ $item->name }}
               </button>
            </h2>
            <div id="flush-collapseOne{{$item->id}}" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
               <div class="accordion-body">
                  @if(count($item->topics->toArray()) != 0)
                  <input style="margin-bottom: 20px; width: 250px" type="text" id="myInput{{$item->id}}" onkeyup="search({{$item->id}})" placeholder="Search">
                  @endif
                  <div class="table-responsive" style="height: 350px">
                     <table id="main-datatable{{$item->id}}" class="list" width="100%">
                        <thead id="main-table-head">
                           @if(count($item->topics->toArray()) != 0)
                              <tr class="list__head">
                                 <th colspan="2"></th>
                                 @foreach($item->topics as $key => $topic)
                                    <th class="topics-label" colspan="{{ count($topic->lessons) }}">
                                       {{ $topic->name }}
                                    </th>
                                 @endforeach 
                              </tr>
                           @endif
                        </thead>
                        <thead id="main-table-body">
                           @if(count($item->topics->toArray()) != 0)
                              <tr class="list__item">
                                 <th>User name</th>
                                 <th>User email</th>
                                 @foreach($item->topics as $key => $topic)
                                 <?php $i = 1; ?>
                                    @foreach($topic->lessons as $key => $lesson)
                                       <th class="lessons-label">№{{ $i }}</th>
                                       <?php $i++; ?>
                                    @endforeach 
                                 @endforeach 
                              </tr>
                           @endif
                        </thead>
                        @if(count($item->topics->toArray()) != 0)
                           <tbody id="main-table-body" class="tbody-{{$item->id}}">
                              @foreach($usersIdx as $key => $res)
                                 <tr id="user-id-{{$res}}">
                                    <td id="user-name-{{$res}}"></td>
                                    <td id="user-email-{{$res}}"></td>
                                    @foreach($item->topics as $key => $topic)
                                       @foreach($topic->lessons as $key => $lesson)
                                          <td onclick="showModal({{$res}}, {{$lesson->id}})" id="lesson-id-{{ $lesson->id}}">0</td>
                                       @endforeach 
                                    @endforeach
                                 </tr>
                              @endforeach  
                           </tbody> 
                        @endif
                     </table>
                  </div>
               </div>
            </div>
         </div>
       </div>
      @endforeach
     @endif
     <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Viewing time</h5>
            <h5 class="modal-title" style="margin-left: 190px;">Ip address</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <div id="times_view" style="display: inline-block;"></div>
            <div id="ip_address" style=" float: right;margin-right: 77px; display: inline-block;"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="closeModal()" data-dismiss="modal">Close</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
@section('css')
<style type="text/css">

.topics-label, .lessons-label{
    text-transform: capitalize
}
.accordion-button::after {
    flex-shrink: 0;
    width: 1.25rem;
    height: 1.25rem;
    margin-left: auto;
    font-family: 'Font Awesome 5 Free';
    left: -4px;
    bottom: 4px;
    position: relative;
    background-size: 1.25rem;
    transition: transform .2s ease-in-out;
}

.accordion-item:last-of-type .accordion-button.collapsed {
    border-bottom-right-radius: calc(.25rem - 1px);
    border-bottom-left-radius: calc(.25rem - 1px);
}

.accordion-flush .accordion-item .accordion-button {
    border-radius: 0;
}

.accordion-flush .accordion-item:last-child {
    border-bottom: 0;
}

.accordion {
    overflow-anchor: none;
}

.accordion-item:last-of-type .accordion-collapse {
    border-bottom-right-radius: .25rem;
    border-bottom-left-radius: .25rem;
}
.accordion-flush .accordion-collapse {
    border-width: 0;
}
.accordion-body {
    padding: 1rem 1.25rem;
}
.accordion-button {
    text-transform: capitalize;
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
    padding: 1rem 1.25rem;
    font-size: 1rem;
    color: #212529;
    text-align: center;
    background-color: #e7f1ff;
    border: 0;
    margin-bottom: 2px;
    border-radius: 0;
    overflow-anchor: none;
    transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out,border-radius .15s ease;
}

.accordion-button:not(.collapsed) {
    background-color: #fff;
}
</style>
@endsection
@section('js')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
<script>
    setTimeout(() => {
    var result = <?php echo json_encode($result2); ?>;
    var result2 =  <?php echo json_encode($result3); ?>;
    var users =  <?php echo json_encode($users); ?>;
    var userIp1 = <?php echo json_encode($userIp); ?>;
    var userIp2 = <?php echo json_encode($userIp2); ?>;
    console.log('User ip', userIp1);
    console.log('User ip2', userIp2);
    setUserInfo(users, result2);
    setStatisticInfo(result2, result);
})

$('#myModal').on('hidden.bs.modal', function () {
  document.getElementById('times_view').innerHTML = '';
   document.getElementById('ip_address').innerHTML = '';
});

const showModal = (userId, lessonId) => {
   var result = <?php echo json_encode($result2); ?>;
   var res = result.filter((res) => {return res.user_id == userId && res.lesson_id == lessonId});
   if (res[0]) {
    console.log('Result ', res[0]);
    const userIpAdresses = JSON.parse(res[0].user_ip_address)
    const timesView = JSON.parse(res[0].times_view);
    $('#myModal').modal('show');
    const timesViewBlock = document.getElementById('times_view');
    const ipAdressBlock = document.getElementById('ip_address');
    console.log(timesView);
    if (timesView == null) {
      const element = document.createElement("p");
      element.innerHTML = 'No data available';
      timesViewBlock.appendChild(element);
    }

    if (userIpAdresses == null) {
      const element = document.createElement("p");
      element.innerHTML = 'No data available';
      ipAdressBlock.appendChild(element);
    }

    timesView?.forEach((time) => {
      const d = new Date(time);
      let formatDateTime = ("0" + d.getDate()).slice(-2) 
                    + "-" + ("0"+(d.getMonth()+1)).slice(-2) 
                    + "-" + d.getFullYear() + " " + ("0" 
                    + d.getHours()).slice(-2) 
                    + ":" + ("0" + d.getMinutes()).slice(-2);

      const element = document.createElement("p");
      element.innerHTML = formatDateTime;
      timesViewBlock.appendChild(element);
      /*ipAdressBlock.appendChild(element);*/
    });

    userIpAdresses?.forEach((ip) => {
        const element = document.createElement("p");
        element.innerHTML = ip;
        ipAdressBlock.appendChild(element);
    });
   }
}

const closeModal = () => {
  document.getElementById('times_view').innerHTML = '';
    document.getElementById('ip_address').innerHTML = '';
  $('#myModal').modal('hide');
}

// search user with name and email
function search(id) {
  var input, filter, table, tr, td,td2, i, txtValue, textValue2;
  input = document.getElementById(`myInput${id}`);
  filter = input.value.toUpperCase();
  table = document.getElementById(`main-datatable${id}`);
  tr = table.getElementsByTagName("tr");
  
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    td2 = tr[i].getElementsByTagName("td")[1];
    if (td && td2) {
      txtValue = td.textContent || td.innerText;
      txtValue2 = td2.textContent || td2.innerText;

      if (txtValue.toUpperCase().indexOf(filter) > -1 || txtValue2.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

// Set user name and email
function setUserInfo(users, result2) {
   users.forEach((user) => {
      result2.forEach((r) => {
         var tbody = document.querySelector(`.tbody-${r}`);
         if (tbody != null) {
            var tr = Array.from(tbody.querySelectorAll('tr'));
            tr.forEach((t) => {
               if (t.getAttribute('id') === `user-id-${user.id}`) {
                  var allTd = Array.from(t.querySelectorAll("td"));
                  allTd.forEach((alt) => {
                     if (alt.getAttribute('id') === `user-name-${user.id}`) {
                        alt.innerHTML = user.name;
                     }
                     if (alt.getAttribute('id') === `user-email-${user.id}`) {
                        alt.innerHTML = user.email;
                     }
                  });
               }
            });
         }
      })
   });
}

// Set statistics info
function setStatisticInfo(result2, result){
    result2.forEach((r) => {
      var tbody = document.querySelector(`.tbody-${r}`);
      if (tbody != null) {
        console.log('Statistic result', result);
         result.forEach((res) => {
            var tr = Array.from(tbody.querySelectorAll('tr'));
            tr.forEach((t) => {
               if (t.getAttribute('id') === `user-id-${res.user_id}`) {
                  var allTd = Array.from(t.querySelectorAll("td"));
                  allTd.forEach((alt) => {
                     if (alt.getAttribute('id') === `lesson-id-${res.lesson_id}`) {
                        alt.innerHTML = res.view_count;
                        alt.style.background = '#93C47D' 
                     }
                  });
               }
            });
         })
      }

    });
}
</script>
@endsection

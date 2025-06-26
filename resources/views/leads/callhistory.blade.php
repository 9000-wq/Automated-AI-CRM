@include('layouts.header')
<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Call Details</strong></h1>

        <div class="row">
            <div class="col-xl-12 col-xxl-12 d-flex">
                <div class="w-100">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card" style='border-radius:10px;'>
                                <div style="width: 100%;background-color: #3b65ea;height: 10px;border-radius: 10px 10px 0px 0px;"></div>
                                <div class="card-body">
                                    @if(!$call)
                                        <div class="alert alert-warning">
                                            No planned calls found for this lead.
                                        </div>
                                        <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-secondary">Back</a>
                                    @else
                                    <form id="crmform" method="POST">
                                        @csrf
                                        <input type="hidden" name="call_id" value="{{ $call->id }}">

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="transcript">Transcript</label>
                                                <textarea name="transcript" id="transcript" class="form-control mb-2 @error('transcript') is-invalid @enderror" rows="5">{{ old('transcript', $call->transcript ?? '') }}</textarea>
                                                @error('transcript')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="sentiment">Sentiment</label>
                                                <select name="sentiment" id="sentiment" class="form-control mb-2 @error('sentiment') is-invalid @enderror">
                                                    <option value="">-- Select Sentiment --</option>
                                                    <option value="positive" {{ old('sentiment', $call->sentiment ?? '') == 'positive' ? 'selected' : '' }}>Positive</option>
                                                    <option value="neutral" {{ old('sentiment', $call->sentiment ?? '') == 'neutral' ? 'selected' : '' }}>Neutral</option>
                                                    <option value="negative" {{ old('sentiment', $call->sentiment ?? '') == 'negative' ? 'selected' : '' }}>Negative</option>
                                                </select>
                                                @error('sentiment')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="outcome">Outcome</label>
                                                <input type="text" name="outcome" id="outcome" class="form-control mb-2 @error('outcome') is-invalid @enderror" value="{{ old('outcome', $call->outcome ?? '') }}">
                                                @error('outcome')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="audio_link">Audio Link</label>
                                                <input type="url" name="audio_link" id="audio_link" class="form-control mb-2 @error('audio_link') is-invalid @enderror" value="{{ old('audio_link', $call->audio_link ?? '') }}">
                                                @error('audio_link')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                        </div>

                                        <button class="btn btn-primary savebtn" type="button">Save Details</button>
                                        <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-secondary ml-2">Cancel</a>
                                        <div class="showalert mt-3"></div>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('.savebtn').on('click', function (e) {
            e.preventDefault();

            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('.showalert').html('');

            $.ajax({
                url: "{{ isset($call) ? route('call.history.update', ['lead' => $lead->id]) : '' }}",
                method: 'POST',
                data: $('#crmform').serialize(),
                success: function (response) {
                    if(response.success) {
                        $('.showalert').html(`<div class="alert alert-success">${response.message}</div>`);
                        
                        setTimeout(() => {
                            if(response.redirect) {
                                window.location.href = response.redirect;
                            }
                        }, 1500);
                    }
                },
                error: function (xhr) {
                    let errors = xhr.responseJSON?.errors || { message: [xhr.responseJSON?.message || 'An error occurred'] };
                    let errorHtml = '<div class="alert alert-danger text-danger"><ul>';
                    
                    $.each(errors, function (key, value) {
                        errorHtml += `<li>${value[0]}</li>`;
                        $(`#${key}`).addClass('is-invalid');
                        $(`#${key}`).after(`<div class="invalid-feedback d-block">${value[0]}</div>`);
                    });
                    
                    errorHtml += '</ul></div>';
                    $('.showalert').html(errorHtml);
                }
            });
        });
    });
</script>
@endpush

@include('layouts.footer')
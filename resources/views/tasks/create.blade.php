@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <form
                class="mx-auto p-3 border col-lg-5 col-md-6 col-11"
                method="POST"
                action="{{ route('tasks.store') }}"
                enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="title">Task title</label>
                    <input
                        type="text"
                        class="form-control"
                        name="title"
                        id="title"
                        placeholder="Title you can recognize quickly"
                        value="{{ old('title') }}"
                        required>
                    @error('title')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="total_word_count">Total word count</label>
                    <input
                        type="text"
                        class="form-control"
                        id="total_word_count"
                        name="total_word_count"
                        placeholder="e.g. 5000 (only numeric)"
                        value="{{ old('total_word_count') }}"
                        required>
                    @error('total_word_count')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="word_count_break">Word count break</label>
                    <small class="text-secondary">( report: 0 + practical: 0 )</small>
                    <input
                        type="text"
                        class="form-control"
                        id="word_count_break"
                        name="word_count_break"
                        placeholder="report: 1000 + practical: 4000"
                        value="{{ old('word_count_break') }}"
                        required>
                    @error('word_count_break')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="country">Country</label>
                    <select name="country" id="country" class="form-control">
                        <option selected disabled>Select country...</option>
                        <option value="Australia">Australia</option>
                        <option value="Canada">Canada</option>
                        <option value="India">India</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="Other">Other</option>
                    </select>
                    @error('country')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="reference_style">Reference style</label>
                    <select name="reference_style" id="reference_style" class="form-control">
                        <option selected disabled>Select reference style...</option>
                        <option value="Harvard">Harvard</option>
                        <option value="Harvard-AGPS">Harvard-AGPS</option>
                        <option value="APA-6th">APA-6th</option>
                        <option value="APA-7th">APA-7th</option>
                        <option value="Chicago">Chicago</option>
                        <option value="IEEE">IEEE</option>
                        <option value="MLA">MLA</option>
                        <option value="other">Other</option>
                    </select>
                    @error('reference_style')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="description">Task description</label>
                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        class="form-control"
                        placeholder="Student prerequisite, country and reference style if not listed, special message or other important information"
                        required>{{ old('description') }}</textarea>
                    @error('description')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="student_deadline">Student deadline</label>
                    <small class="text-secondary">( Task has to be delivered to client within this date )</small>
                    <input
                        type="datetime-local"
                        name="student_deadline"
                        id="student_deadline"
                        class="form-control"
                        value="{{ old('student_deadline') }}"
                        required>
                    @error('student_deadline')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="tutor_deadline">Tutor deadline</label>
                    <small class="text-secondary">( Should be less than student dl )</small>
                    <input
                        type="datetime-local"
                        name="tutor_deadline"
                        id="tutor_deadline"
                        class="form-control"
                        value="{{ old('tutor_deadline') }}"
                        required>
                    @error('tutor_deadline')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="requirement_file">Requirement file</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="requirement_file" name="requirement_file">
                        <label class="custom-file-label" for="requirement_file">Choose file</label>
                    </div>
                    @error('requirement_file')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
@endsection

import 'dart:async';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/exams_to_all_students/data/repositories/exams_to_all_students_repositories_implementation.dart';
import '/features/exams_to_all_students/presentation/managers/cubits/exams_to_all_students_state.dart';
import '/features/exams_to_all_students/presentation/managers/models/exams_to_all_students_model.dart';

class ExamsCubit extends Cubit<ExamsState> {
  ExamsCubit({required this.examsRepositoriesImplementation})
    : super(ExamsInitialState());
  final ExamsRepositoriesImplementation examsRepositoriesImplementation;
  Timer? timer;
  Future<void> getExamsByDate({required String date}) async {
    emit(ExamsLoadingState());
    final branchId = await StoreParametersInSharedPreferences.getIntParameter(
      key: keyInstituteBranchIdInSharedPreferences,
    );
    final result = await examsRepositoriesImplementation.getExamsByDate(
      date: date,
      branchId: branchId ?? 1,
    );
    result.fold(
      (failure) {
        emit(
          ExamsFailureState(
            errorMessageInCubit: failure.errorMessageInFailureError,
          ),
        );
      },
      (exams) {
        emit(ExamsSuccessState(examsListInCubit: exams));
        startCheckingTime();
        //start make the Timer work(do rebuild), after get on exams
      },
    );
  }

  void startCheckingTime() {
    //this method for update the ui, and from this update will determined appear CustomCheckWidget or CustomCircleWidget
    timer?.cancel();
    timer = Timer.periodic(const Duration(minutes: 5), (_) => checkExamsTime());
    checkExamsTime();
    //this trigger to method work immediately(without time wait to trigger), because if the exams in 9:00 and the user open the app in 9:02 so should on user wait 5 minutes to see the updates or should happen update immediately and after determined time
  }

  void checkExamsTime() {
    //this method will check from exam time is same exam_time in backend or no
    if (state is! ExamsSuccessState) return;
    //if the state not Success so sure there are no exams, so getout from this method
    final currentState = state as ExamsSuccessState;
    //i assign state to enable me access on examsListInCubit
    final List<ExamsModel> exams = List<ExamsModel>.from(
      currentState.examsListInCubit,
    );
    //create list and give it elements from this list(same elements it)
    final now = DateTime.now();
    bool hasChange = false;
    //i use this parameter to ask this question(Did anything change in the list), and i must ask because wrong emitting state without changes(this is waste + rebuild spam)
    for (int i = 0; i < exams.length; i++) {
      final exam = exams[i];
      //i will take full elements, and i take them exam by exam
      if (exam.isChecked) continue;
      //if this exam is already checked → skip it(i mean this exam contain on isChecked attribute and if value this attribute is true so skip(continue) i don't need to do anything on this exam)
      final parts = exam.firstTime?.split(':');
      //it's mean give full String but just cut this part(:) i don't need it
      if (parts == null || parts.length < 2) continue;
      //if the time was null(there is no time) or length the time smaller than 2 parts(just 09(hour)), so skip(continue) this exam
      final examDateTime = DateTime(
        now.year,
        now.month,
        now.day,
        int.parse(parts[0]),
        int.parse(parts[1]),
      );
      //i create time object and i give it time(this year, this month, this day, hour from backend, minute from backend), i do that because i want to do compare
      if (now.isAfter(examDateTime) || now.isAtSameMomentAs(examDateTime)) {
        //if the object time now(example DateTime(2025, 12, 24, 9, 0)) is after examDateTime object or eqaul it so give this two parameters true value
        exam.isChecked = true;
        //this parameter to determined if appear CustomCheckWidget or CustomCircleWidget
        hasChange = true;
        //this parameter to tell the state you should emit new state because happend change(and i emit SuccessState because i put the timer and make it work in SuccessState just)
      }
    }
    if (hasChange) {
      emit(ExamsSuccessState(examsListInCubit: exams));
    } //if happend change so emit new state
  }

  @override
  Future<void> close() {
    timer?.cancel();
    return super.close();
  } //dispose from timer when the cubit is close
}

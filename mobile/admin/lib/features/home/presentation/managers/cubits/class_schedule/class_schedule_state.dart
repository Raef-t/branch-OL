import '/features/home/presentation/managers/models/class_schedule/class_schedule_model.dart';

abstract class ClassScheduleState {}

class ClassScheduleInitialState extends ClassScheduleState {}

class ClassScheduleLoadingState extends ClassScheduleState {}

class ClassScheduleSuccessState extends ClassScheduleState {
  final ClassScheduleModel classScheduleModelInCubit;
  ClassScheduleSuccessState({required this.classScheduleModelInCubit});
}

class ClassScheduleFailureState extends ClassScheduleState {
  final String errorMessageInCubit;
  ClassScheduleFailureState({required this.errorMessageInCubit});
}

import 'package:flutter/material.dart';
import '/features/courses/presentation/view/widgets/custom_courses_view_body.dart';
import '/features/home/presentation/view/widgets/custom_home_view_body.dart';
import '/features/profile/presentation/view/widgets/custom_profile_view_body.dart';
import '/features/teachers/presentation/view/widgets/custom_teachers_view_body.dart';

final List<Widget> customViewBodiesList = const [
  CustomHomeViewBody(),
  CustomCoursesViewBody(),
  CustomTeachersViewBody(),
  CustomProfileViewBody(),
];

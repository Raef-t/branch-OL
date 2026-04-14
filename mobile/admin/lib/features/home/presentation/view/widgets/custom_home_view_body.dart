// ignore_for_file: use_build_context_synchronously
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '/core/components/sliver_app_bar_to_hole_app_component.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/courses/presentation/managers/models/academic_branches/academic_branches_to_courses_model.dart';
import '/features/courses_details/presentation/managers/cubit/academic_branches_courses_details_cubit.dart';
import '/features/exams_to_all_students/presentation/managers/cubits/exams_to_all_students_cubit.dart';
import '/features/home/presentation/managers/cubits/batch_average/batch_average_cubit.dart';
import '/features/home/presentation/view/widgets/custom_app_bar_widget_in_home_view.dart';
import '/features/home/presentation/view/widgets/custom_sliver_fill_remaining_home_view.dart';

class CustomHomeViewBody extends StatefulWidget {
  const CustomHomeViewBody({super.key});

  @override
  State<CustomHomeViewBody> createState() => _CustomHomeViewBodyState();
}

class _CustomHomeViewBodyState extends State<CustomHomeViewBody> {
  AcademicBranchesToCoursesModel? selectedValue;
  String? userName, userPhoto;
  int? instituteBranchId;
  @override
  void initState() {
    super.initState();
    _init();
  }

  Future<void> _init() async {
    userName =
        await StoreParametersInSharedPreferences.getStringParameter(
          key: keyUserNameInSharedPreferences,
        ) ??
        '';

    userPhoto =
        await StoreParametersInSharedPreferences.getStringParameter(
          key: keyUserPhotoInSharedPreferences,
        ) ??
        '';

    instituteBranchId =
        await StoreParametersInSharedPreferences.getIntParameter(
          key: keyInstituteBranchIdInSharedPreferences,
        );
    final date = DateFormat('yyyy-MM-dd').format(DateTime.now());
    await context.read<ExamsCubit>().getExamsByDate(date: date);
  }

  @override
  Widget build(BuildContext context) {
    return CustomScrollView(
      slivers: [
        SliverAppBarToHoleAppComponent(
          appBarWidget: CustomAppBarWidgetInHomeView(
            userName: userName ?? 'لا يوجد',
            userPhoto: userPhoto ?? 'لا يوجد',
          ),
        ),
        CustomSliverFillRemainingHomeView(
          selectedValue: selectedValue,
          onSelected: (value) async {
            await StoreParametersInSharedPreferences.saveIntParameter(
              intValue: value.id ?? 1,
              key: keyAcademicBranchIdInSharedPreferences,
            );
            context.read<AcademicBranchesCoursesDetailsCubit>().selectBranch(
              value,
            );
            await context.read<BatchAverageCubit>().getBatchAverages();
          },
        ),
        SliverPadding(
          padding: EdgeInsets.only(
            bottom:
                MediaQuery.of(context).padding.bottom +
                kBottomNavigationBarHeight,
          ),
        ),
      ],
    );
  }
}

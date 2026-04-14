import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/border_radius/circulars.dart';
import '/core/components/app_bar_widget_with_right_arrow_image_and_three_texts_component.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/core/components/search_text_field_component.dart';
import '/core/components/sliver_app_bar_to_hole_app_component.dart';
import '/core/components/text_medium12_component.dart';
import '/core/components/text_medium14_component.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/core/sized_boxs/heights.dart';
import '/core/styles/colors_style.dart';
import '/features/teachers/presentation/managers/cubits/teachers_cubit.dart';
import '/features/teachers/presentation/managers/cubits/teachers_state.dart';
import '/gen/fonts.gen.dart';

class CustomTeachersViewBody extends StatelessWidget {
  const CustomTeachersViewBody({super.key});

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    return CustomScrollView(
      slivers: [
        const SliverAppBarToHoleAppComponent(
          appBarWidget: AppBarWidgetWithRightArrowImageAndThreeTextsComponent(
            firstText: 'المدرسون',
            secondText: 'يمكنك الاطلاع على جميع المدرسون في',
            thirdText: 'المعهد',
          ),
        ),
        SliverFillRemaining(
          hasScrollBody: false,
          child: BackgroundBodyToViewsComponent(
            child: Column(
              children: [
                Heights.height20(context: context),
                OnlyPaddingWithChild.left39AndRight20(
                  context: context,
                  child: const SearchTextFieldComponent(),
                ),
                Heights.height38(context: context),
                BlocBuilder<TeachersCubit, TeachersState>(
                  builder: (context, state) {
                    if (state is TeachersSuccessState) {
                      final teachersList = state.teachersListInCubit;
                      final length = teachersList.length;
                      return Column(
                        children: List.generate(length, (index) {
                          final teachersModel = teachersList[index];
                          return Card(
                            margin:
                                OnlyPaddingWithoutChild.left20AndRight20AndBottom8(
                                  context: context,
                                ),
                            color: ColorsStyle.whiteColor,
                            elevation: 0,
                            shape: RoundedRectangleBorder(
                              borderRadius: Circulars.circular10(
                                context: context,
                              ),
                            ),
                            child: Directionality(
                              textDirection: TextDirection.rtl,
                              child: ListTile(
                                leading: SizedBox(
                                  height: size.height * 0.05,
                                  width: size.height * 0.048,
                                  child: ClipOval(
                                    child: Image.network(
                                      teachersModel.photo != null &&
                                              teachersModel.photo!.isNotEmpty
                                          ? teachersModel.photo!
                                          : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTpR2mt4DTP5bMkhpMu1eMde4Rg6EFc78CfIg&s',
                                      fit: BoxFit.fill,
                                      errorBuilder: (context, error, stackTrace) {
                                        return Image.network(
                                          'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTpR2mt4DTP5bMkhpMu1eMde4Rg6EFc78CfIg&s',
                                          fit: BoxFit.fill,
                                        );
                                      },
                                    ),
                                  ),
                                ),
                                title: TextMedium14Component(
                                  text: teachersModel.name ?? 'لا يوجد اسم',
                                  color: ColorsStyle.mediumBlackColor2,
                                ),
                                subtitle: TextMedium12Component(
                                  text:
                                      'الاختصاص: '
                                      '${teachersModel.specialization}',
                                  fontFamily: FontFamily.tajawal,
                                  color: ColorsStyle.greyColor,
                                ),
                              ),
                            ),
                          );
                        }),
                      );
                    } else if (state is TeachersFailureState) {
                      return FailureStateComponent(
                        errorText: state.errorMessageInCubit,
                      );
                    } else {
                      return const CircleLoadingStateComponent();
                    }
                  },
                ),
              ],
            ),
          ),
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
